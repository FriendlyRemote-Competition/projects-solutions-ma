<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\TimetableService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    private $timetable;

    public function __construct(TimetableService $timetable) {
        $this->timetable = $timetable;
    }

    public function store(Request $request)
    {
        $request->validate([
            'departure_code' => 'required',
            'first_name' => 'required|max:60',
            'last_name' => 'required|max:60',
            'email' => 'required|email',
            'seats' => 'required|integer|min:1|max:16'
        ]);

        $parts = explode('-', $request->departure_code);
        if (count($parts) !== 4){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 
        
        $lineCode = $parts[0];
        $dateStr = Carbon::createFromFormat('Ymd', $parts[1])->format('Y-m-d');
        
        $deps = $this->timetable->getDepartures($lineCode, $dateStr);
        if ($deps === null){
            return response()->json(['message' => 'Resource not found'], 404);
        } 

        $dep = collect($deps)->firstWhere('code', $request->departure_code);
        if (!$dep){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 

        if ($dep['status'] !== 'scheduled' || $dep['seats_available'] < $request->seats) {
            return response()->json([
                'message' => 'Not enough seats available'
            ], 422);
        }

        $fare = $dep['fare_cny'];
        $code = 'HPF-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'booking_code' => $code,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'seats' => $request->seats,
            'fare_cny' => $fare,
            'total_fare_cny' => $fare * $request->seats,
            'departure_code' => $request->departure_code,
            'departure_date' => $dep['departure_date'],
            'departure_time' => $dep['departure_time'],
            'line_code' => $lineCode
        ]);

        return response()->json([
            'data' => $this->formatBooking($booking)
        ], 201);
    }

    public function lookup(Request $request)
    {
        $booking = $this->findAndVerify($request);
        if (!$booking){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 
        return response()->json([
            'data' => $this->formatBooking($booking)
        ]);
    }

    public function update(Request $request, $code)
    {
        $request->merge([
            'booking_code' => $code
        ]);
        $booking = $this->findAndVerify($request);
        if (!$booking){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Booking is already cancelled'
            ], 422);
        }

        $request->validate([
            'seats' => 'required|integer|min:1|max:16'
        ]);

        $parts = explode('-', $booking->departure_code);
        $deps = $this->timetable->getDepartures($parts[0], $booking->departure_date);
        $dep = collect($deps)->firstWhere('code', $booking->departure_code);

        $realAvailable = $dep['seats_available'] + $booking->seats;
        if ($realAvailable < $request->seats) {
            return response()->json([
                'message' => 'Not enough seats available'
            ], 422);
        }

        $booking->update([
            'seats' => $request->seats,
            'total_fare_cny' => $booking->fare_cny * $request->seats
        ]);

        return response()->json([
            'data' => $this->formatBooking($booking)
        ]);
    }

    public function cancel(Request $request, $code)
    {
        $request->merge(['booking_code' => $code]);
        $booking = $this->findAndVerify($request);
        if (!$booking){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Booking is already cancelled'
            ], 422);
        }

        $depDateTime = Carbon::parse($booking->departure_date . ' ' . $booking->departure_time);
        if (now()->addMinutes(5)->gte($depDateTime)) {
            return response()->json([
                'message' => 'Booking closed for this departure'
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled', 
            'cancelled_at' => now()
        ]);
        return response()->json([
            'data' => $this->formatBooking($booking)
        ]);
    }

    private function findAndVerify(Request $request) {
        $b = Booking::where('booking_code', $request->booking_code)->first();
        if (!$b) return false;
        if (strtolower(trim($b->first_name)) !== strtolower(trim($request->first_name))){
            return false;
        } 
        if (strtolower(trim($b->last_name)) !== strtolower(trim($request->last_name))){
            return false;
        } 
        return $b;
    }

    private function formatBooking($b) {
        return [
            'booking_code' => $b->booking_code,
            'status' => $b->status,
            'first_name' => $b->first_name,
            'last_name' => $b->last_name,
            'email' => $b->email,
            'phone' => $b->phone,
            'seats' => $b->seats,
            'fare_cny' => number_format($b->fare_cny, 2, '.', ''),
            'total_fare_cny' => number_format($b->total_fare_cny, 2, '.', ''),
            'departure_code' => $b->departure_code,
            'created_at' => $b->created_at->toIso8601String(),
            'updated_at' => $b->updated_at->toIso8601String(),
            'cancelled_at' => $b->cancelled_at ? $b->cancelled_at->toIso8601String() : null
        ];
    }
}