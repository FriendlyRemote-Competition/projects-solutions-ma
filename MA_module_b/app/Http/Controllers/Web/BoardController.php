<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\Line;
use App\Services\TimetableService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BoardController extends Controller
{
    public function index()
    {
        $stations = Station::orderBy('code')->get();
        return view('board.index', compact('stations'));
    }

    public function show(Request $request, $code)
    {
        $station = Station::where('code', $code)->firstOrFail();
        $limit = $request->get('limit', 8);
        $limit = ($limit >= 1 && $limit <= 20) ? $limit : 8;

        $lines = Line::where('station_a_code', $code)->orWhere('station_b_code', $code)->get();
        $timetable = new TimetableService();
        
        $departures = [];
        $now = now();
        $todayStr = $now->format('Y-m-d');
        $tomorrowStr = $now->copy()->addDay()->format('Y-m-d');

        foreach ($lines as $line) {
            $depsToday = $timetable->getDepartures($line->code, $todayStr, $station->code);
            $depsTomorrow = $timetable->getDepartures($line->code, $tomorrowStr, $station->code);
            $departures = array_merge($departures, $depsToday, $depsTomorrow);
        }

        $departures = collect($departures)->filter(function($d) {
            return $d['status'] !== 'departed';
        })->sortBy(function($d) {
            return $d['departure_date'] . ' ' . $d['departure_time'];
        })->take($limit)->map(function($d) use ($now) {
            $depTime = Carbon::parse($d['departure_date'] . ' ' . $d['departure_time']);
            $diffMinutes = $now->diffInMinutes($depTime);
            
            if ($diffMinutes == 0) $inStr = "0 minutes";
            else {
                $hours = floor($diffMinutes / 60);
                $mins = $diffMinutes % 60;
                $inStr = '';
                if ($hours > 0) $inStr .= "$hours hour" . ($hours > 1 ? 's ' : ' ');
                $inStr .= "$mins minute" . ($mins != 1 ? 's' : '');
            }

            return [
                'time' => $d['departure_time'],
                'line_name' => Line::where('code', explode('-', $d['code'])[0])->value('name'),
                'destination' => $d['destination']['name'],
                'in' => trim($inStr),
                'available' => $d['status'] === 'cancelled' ? '-' : $d['seats_available'],
                'status' => $d['status']
            ];
        });

        return view('board.station', compact('station', 'departures', 'limit'));
    }
}