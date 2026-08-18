<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Admins
        $admins = array_map('str_getcsv', file(__DIR__ . '/admins.csv'));
        array_shift($admins);
        foreach ($admins as $row) {
            if(count($row) < 5) continue;
            DB::table('admins')->insert([
                'email' => $row[0],
                'password' => Hash::make($row[1]),
                'name' => $row[2],
                'role' => $row[3],
                'is_active' => (bool)$row[4],
            ]);
        }

        // Stations
        $stations = array_map('str_getcsv', file(__DIR__ . '/stations.csv'));
        array_shift($stations);
        foreach ($stations as $row) {
            if(count($row) < 2) continue;
            DB::table('stations')->insert([
                'code' => $row[0], 'name' => $row[1], 'bank' => $row[2] ?? null,
                'district' => $row[3] ?? null, 'address' => $row[4] ?? null
            ]);
        }

        // Lines & Service Windows
        $linesCsv = array_map('str_getcsv', file(__DIR__ . '/lines.csv'));
        array_shift($linesCsv);
        $insertedLines = [];
        foreach ($linesCsv as $row) {
            if(count($row) < 13) continue;
            $code = $row[0];
            if (!in_array($code, $insertedLines)) {
                DB::table('lines')->insert([
                    'code' => $code, 'name' => $row[1], 'status' => $row[2],
                    'station_a_code' => $row[3], 'station_b_code' => $row[5],
                    'seat_capacity' => $row[7], 'crossing_minutes' => $row[8], 'fare_cny' => $row[9]
                ]);
                $insertedLines[] = $code;
            }
            if ($row[10]) {
                DB::table('service_windows')->insert([
                    'line_code' => $code, 'start_time' => $row[10],
                    'end_time' => $row[11], 'interval_minutes' => $row[12]
                ]);
            }
        }

        // Cancelled Departures
        $cancels = array_map('str_getcsv', file(__DIR__ . '/cancelled_departures.csv'));
        array_shift($cancels);
        foreach ($cancels as $row) {
            if(count($row) < 6) continue;
            $depCode = "{$row[0]}-" . str_replace('-', '', $row[1]) . "-" . str_replace(':', '', $row[2]) . "-{$row[3]}";
            DB::table('cancelled_departures')->insert([
                'departure_code' => $depCode,
                'reason' => $row[4],
                'cancelled_at' => Carbon::parse($row[5])
            ]);
        }
    }
}