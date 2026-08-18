<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Line;
use App\Models\Station;
use App\Services\TimetableService;
use Illuminate\Http\Request;

class LineController extends Controller
{
    public function index()
    {
        $lines = Line::with(['serviceWindows' => function($q) {
            $q->orderBy('start_time');
        }])->orderBy('code')->get()->map(function($line) {
            return $this->formatLine($line);
        });
        return response()->json([
            'data' => $lines
        ]);
    }

    public function show($code)
    {
        $line = Line::with('serviceWindows')->where('code', $code)->first();
        if (!$line){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 
        return response()->json([
            'data' => $this->formatLine($line)
        ]);
    }

    public function timetable(Request $request, $code)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d'
        ]);
        $date = $request->date ?? now()->format('Y-m-d');
        
        $line = Line::where('code', $code)->first();
        if (!$line){
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        } 

        if ($request->station && !in_array($request->station, [$line->station_a_code, $line->station_b_code])) {
            return response()->json([
                'message' => 'Validation failed', 
                'errors' => [
                    'station' => ['Invalid station for this line.']
                ]
            ], 422);
        }

        $service = new TimetableService();
        $departures = $service->getDepartures($code, $date, $request->station);

        return response()->json([
            'data' => $departures
        ]);
    }

    private function formatLine($line) {
        return [
            'code' => $line->code,
            'name' => $line->name,
            'status' => $line->status,
            'station_a' => ['code' => $line->station_a_code, 'name' => Station::find($line->station_a_code)->name],
            'station_b' => ['code' => $line->station_b_code, 'name' => Station::find($line->station_b_code)->name],
            'seat_capacity' => $line->seat_capacity,
            'crossing_minutes' => $line->crossing_minutes,
            'fare_cny' => number_format($line->fare_cny, 2, '.', ''),
            'service_windows' => $line->serviceWindows->map(function($w) {
                return [
                    'start_time' => substr($w->start_time, 0, 5), 
                    'end_time' => substr($w->end_time, 0, 5), 
                    'interval_minutes' => $w->interval_minutes
                ];
            })
        ];
    }
}