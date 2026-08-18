<?php

namespace App\Services;

use App\Models\Line;
use App\Models\Booking;
use App\Models\Station;
use App\Models\CancelledDeparture;
use Carbon\Carbon;

class TimetableService
{
    public function getDepartures($lineCode, $dateStr, $filterStation = null)
    {
        $line = Line::with(['serviceWindows' => function($q) {
            $q->orderBy('start_time', 'asc');
        }])->where('code', $lineCode)->first();

        if (!$line) return null;
        if ($line->status === 'suspended') return [];

        $date = Carbon::parse($dateStr);
        $dateFormatted = $date->format('Ymd');
        
        $bookings = Booking::where('line_code', $line->code)
            ->where('departure_date', $date->format('Y-m-d'))
            ->where('status', 'confirmed')
            ->selectRaw('departure_code, SUM(seats) as total')
            ->groupBy('departure_code')
            ->pluck('total', 'departure_code')
            ->toArray();

        $cancellations = CancelledDeparture::where('departure_code', 'like', "{$line->code}-{$dateFormatted}-%")
            ->pluck('reason', 'departure_code')
            ->toArray();

        $departures = [];
        $now = now();

        foreach ($line->serviceWindows as $window) {
            $current = Carbon::parse($dateStr . ' ' . $window->start_time);
            $end = Carbon::parse($dateStr . ' ' . $window->end_time);

            while ($current->lte($end)) {
                $timeStr = $current->format('H:i');
                $legs = [
                    ['o' => $line->station_a_code, 'd' => $line->station_b_code],
                    ['o' => $line->station_b_code, 'd' => $line->station_a_code],
                ];

                foreach ($legs as $leg) {
                    if ($filterStation && $filterStation !== $leg['o']) continue;

                    $depCode = "{$line->code}-{$dateFormatted}-{$current->format('Hi')}-{$leg['o']}";
                    $isCancelled = array_key_exists($depCode, $cancellations);
                    
                    $depDateTime = Carbon::parse($dateStr . ' ' . $timeStr);
                    $status = 'scheduled';
                    if ($isCancelled) $status = 'cancelled';
                    elseif ($depDateTime->lte($now)) $status = 'departed';

                    $booked = $bookings[$depCode] ?? 0;
                    
                    $departures[] = [
                        'code' => $depCode,
                        'origin' => $this->stationFormat($leg['o']),
                        'destination' => $this->stationFormat($leg['d']),
                        'departure_date' => $date->format('Y-m-d'),
                        'departure_time' => $timeStr,
                        'arrival_time' => $current->copy()->addMinutes($line->crossing_minutes)->format('H:i'),
                        'seats_booked' => (int)$booked,
                        'seats_available' => $line->seat_capacity - $booked,
                        'fare_cny' => number_format($line->fare_cny, 2, '.', ''),
                        'status' => $status,
                        'cancellation_reason' => $cancellations[$depCode] ?? null,
                    ];
                }
                $current->addMinutes($window->interval_minutes);
            }
        }

        usort($departures, function ($a, $b) {
            if ($a['departure_time'] === $b['departure_time']) {
                return $a['origin']['code'] <=> $b['origin']['code'];
            }
            return $a['departure_time'] <=> $b['departure_time'];
        });

        return $departures;
    }

    private function stationFormat($code) {
        $name = Station::where('code', $code)->value('name');
        return ['code' => $code, 'name' => $name];
    }
}