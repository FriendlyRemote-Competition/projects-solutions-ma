<!DOCTYPE html>
<html>
<head>
    <title>Statistics</title>
    <style>
        body{
            font-family:sans-serif; 
            padding:20px;
        } 
        table{
            width:100%; 
            border-collapse:collapse; 
            margin-bottom:30px;
        } 
        th, td{
            border:1px solid #ccc; 
            padding:10px; 
            text-align:left;
        } 
        th{
            background:#eee;
        }
    </style>
</head>
<body>
    <h1>Statistics for {{ $dateStr }}</h1>
    
    <h2>Total Summary</h2>
    <table>
        <tr>
            <th>Total Departures</th>
            <td>{{ $stats['total']['departures'] }}</td>
        </tr>
        <tr>
            <th>Cancelled Departures</th>
            <td>{{ $stats['total']['cancelled_deps'] }}</td>
        </tr>
        <tr>
            <th>Total Bookings</th>
            <td>{{ $stats['total']['bookings'] }}</td>
        </tr>
        <tr>
            <th>Cancelled Bookings</th>
            <td>{{ $stats['total']['cancelled_bookings'] }}</td>
        </tr>
        <tr>
            <th>Seats Booked</th>
            <td>{{ $stats['total']['seats'] }}</td>
        </tr>
        <tr>
            <th>Revenue (CNY)</th>
            <td>¥{{ number_format($stats['total']['revenue'], 2) }}</td>
        </tr>
    </table>

    <h2>By Line</h2>
    <table>
        <tr>
            <th>Line</th>
            <th>Departures</th>
            <th>Cancelled Deps</th>
            <th>Bookings</th>
            <th>Cancelled Bookings</th>
            <th>Seats Booked</th>
            <th>Revenue (CNY)</th>
        </tr>
        @foreach($stats['lines'] as $line)
        <tr>
            <td>{{ $line['code'] }} - {{ $line['name'] }}</td>
            <td>{{ $line['departures'] }}</td>
            <td>{{ $line['cancelled_deps'] }}</td>
            <td>{{ $line['bookings'] }}</td>
            <td>{{ $line['cancelled_bookings'] }}</td>
            <td>{{ $line['seats'] }}</td>
            <td>¥{{ number_format($line['revenue'], 2) }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>