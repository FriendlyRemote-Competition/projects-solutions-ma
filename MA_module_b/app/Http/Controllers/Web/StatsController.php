<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Line;
use App\Models\Booking;
use App\Services\TimetableService;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $dateStr = $request->get('date', now()->format('Y-m-d'));
        $lines = Line::all();
        $timetable = new TimetableService();

        $stats = [
            'total' => [
                'departures' => 0, 
                'cancelled_deps' => 0, 
                'bookings' => 0, 
                'cancelled_bookings' => 0, 
                'seats' => 0, 
                'revenue' => 0
            ],
            'lines' => []
        ];

        foreach ($lines as $line) {
            $deps = collect($timetable->getDepartures($line->code, $dateStr));
            $scheduledDeps = $deps->where('status', '!=', 'cancelled')->count();
            $cancelledDeps = $deps->where('status', 'cancelled')->count();

            $bookings = Booking::where('line_code', $line->code)->where('departure_date', $dateStr)->get();
            $confBookings = $bookings->where('status', 'confirmed');
            $cancBookings = $bookings->where('status', 'cancelled');

            $lineStats = [
                'code' => $line->code, 
                'name' => $line->name,
                'departures' => $scheduledDeps,
                'cancelled_deps' => $cancelledDeps,
                'bookings' => $bookings->count(),
                'cancelled_bookings' => $cancBookings->count(),
                'seats' => $confBookings->sum('seats'),
                'revenue' => $confBookings->sum('total_fare_cny')
            ];

            $stats['lines'][] = $lineStats;
            
            $stats['total']['departures'] += $scheduledDeps;
            $stats['total']['cancelled_deps'] += $cancelledDeps;
            $stats['total']['bookings'] += $bookings->count();
            $stats['total']['cancelled_bookings'] += $cancBookings->count();
            $stats['total']['seats'] += $lineStats['seats'];
            $stats['total']['revenue'] += $lineStats['revenue'];
        }

        return view('stats.index', compact('stats', 'dateStr'));
    }
}