<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Line;
use App\Models\Booking;
use App\Models\CancelledDeparture;
use App\Models\ServiceWindow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $admin = Admin::where('email', $request->email)->where('is_active', true)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $admin->api_token = Str::random(60);
        $admin->save();

        return response()->json(['data' => [
            'token' => $admin->api_token,
            'user' => [
                'email' => $admin->email, 
                'name' => $admin->name, 
                'role' => $admin->role
            ]
        ]]);
    }

    public function bookings(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'line_code' => 'nullable|exists:lines,code',
            'status' => 'nullable|in:confirmed,cancelled',
            'page' => 'nullable|integer|min:1'
        ]);

        $query = Booking::query();
        
        if ($request->date) $query->where('departure_date', $request->date);
        else $query->where('departure_date', now()->format('Y-m-d'));

        if ($request->line_code) $query->where('line_code', $request->line_code);
        if ($request->status) $query->where('status', $request->status);
        
        if ($request->search) {
            $s = "%{$request->search}%";
            $query->where(function($q) use ($s) {
                $q->where('booking_code', 'like', $s)
                  ->orWhere('first_name', 'like', $s)
                  ->orWhere('last_name', 'like', $s)
                  ->orWhere('email', 'like', $s);
            });
        }

        $paginator = $query->orderBy('departure_date')
                           ->orderBy('departure_time')
                           ->orderBy('booking_code')
                           ->paginate(15);
        
        $data = $paginator->map(function($b) {
            return (new BookingController(new \App\Services\TimetableService))->formatBooking($b);
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total()
            ]
        ]);
    }

    public function storeLine(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:2|unique:lines|regex:/^[A-Z]+$/',
            'station_a_code' => 'required|exists:stations,code|different:station_b_code',
            'station_b_code' => 'required|exists:stations,code',
            'seat_capacity' => 'required|integer|min:1|max:500',
            'crossing_minutes' => 'required|integer|min:1|max:120',
            'fare_cny' => 'required|numeric|min:0|max:999.99',
            'status' => 'nullable|in:active,suspended'
        ]);

        $line = Line::create($request->all());
        return app(LineController::class)->show($line->code)->setStatusCode(201);
    }

    public function updateLine(Request $request, $code)
    {
        $line = Line::where('code', $code)->firstOrFail();
        $request->validate([
            'station_a_code' => 'required|exists:stations,code|different:station_b_code',
            'station_b_code' => 'required|exists:stations,code',
            'seat_capacity' => 'required|integer|min:1|max:500',
            'crossing_minutes' => 'required|integer|min:1|max:120',
            'fare_cny' => 'required|numeric|min:0|max:999.99',
            'status' => 'nullable|in:active,suspended'
        ]);

        $maxSeatsBooked = Booking::where('line_code', $line->code)
            ->where('status', 'confirmed')
            ->where('departure_date', '>=', now()->format('Y-m-d'))
            ->selectRaw('SUM(seats) as total')
            ->groupBy('departure_code')
            ->get()->max('total') ?? 0;

        if ($request->seat_capacity < $maxSeatsBooked) {
            return response()->json([
                'message' => 'Capacity is lower than seats already booked'
            ], 422);
        }

        $line->update($request->all());
        return app(LineController::class)->show($line->code);
    }

    public function storeServiceWindow(Request $request, $code)
    {
        $line = Line::where('code', $code)->firstOrFail();
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'interval_minutes' => "required|integer|min:{$line->crossing_minutes}|max:120"
        ]);

        $overlaps = ServiceWindow::where('line_code', $line->code)
            ->where(function($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('start_time', '<=', $request->start_time)->where('end_time', '>=', $request->end_time);
                  });
            })->exists();

        if ($overlaps){
            return response()->json([
                'message' => 'Service window overlaps an existing window'
            ], 422);
        } 

        $sw = ServiceWindow::create(array_merge($request->all(), ['line_code' => $line->code]));
        return response()->json([
            'start_time' => substr($sw->start_time, 0, 5), 
            'end_time' => substr($sw->end_time, 0, 5), 
            'interval_minutes' => $sw->interval_minutes
        ], 201);
    }

    public function destroyServiceWindow($code, $start_time)
    {
        $sw = ServiceWindow::where('line_code', $code)->where('start_time', $start_time . ':00')->first();
        if (!$sw){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 
        
        $sw->delete();
        return response()->json([
            'message' => 'Service window deleted'
        ]);
    }

    public function cancelDeparture(Request $request, $code)
    {
        $request->validate([
            'reason' => 'nullable|max:200'
        ]);

        $parts = explode('-', $code);
        if(count($parts) !== 4){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 
        $dateStr = Carbon::createFromFormat('Ymd', $parts[1])->format('Y-m-d');
        
        $deps = app(TimetableService::class)->getDepartures($parts[0], $dateStr);
        $dep = collect($deps)->firstWhere('code', $code);
        
        if (!$dep){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 
        if ($dep['status'] === 'cancelled'){
            return response()->json([
                'message' => 'Departure is already cancelled'
            ], 422);
        } 
        if ($dep['status'] === 'departed') {
            return response()->json([
                'message' => 'Departure has already departed'
            ], 422);
        }
        CancelledDeparture::create([
            'departure_code' => $code, 
            'reason' => $request->reason, 
            'cancelled_at' => now()
        ]);

        $affected = Booking::where('departure_code', $code)->where('status', 'confirmed')->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return response()->json([
            'data' => ['affected_bookings' => $affected]
        ]);
    }
}