<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="10">
    <title>Departures: {{ $station->name }}</title>
    <style>
        body { 
            font-family: sans-serif; 
            background: #121212; 
            color: white; 
            padding: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 1rem; 
        }
        th, td { 
            text-align: left; 
            padding: 15px; 
            border-bottom: 1px solid #333; 
            font-size: 1.2rem; 
        }
        th { 
            background: #222; 
        }
        .scheduled { 
            color: #4ade80; 
        }
        .cancelled { 
            color: #f87171; 
            text-decoration: line-through; 
        }
    </style>
</head>
<body>
    <h1>Next Departures - {{ $station->name }}</h1>
    <table>
        <tr>
            <th>Time</th>
            <th>Line</th>
            <th>Destination</th>
            <th>Departure In</th>
            <th>Available Seats</th>
            <th>Status</th>
        </tr>
        @foreach($departures as $dep)
            <tr class="{{ $dep['status'] }}">
                <td>{{ $dep['time'] }}</td>
                <td>{{ $dep['line_name'] }}</td>
                <td>{{ $dep['destination'] }}</td>
                <td>{{ $dep['in'] }}</td>
                <td>{{ $dep['available'] }}</td>
                <td>{{ strtoupper($dep['status']) }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>