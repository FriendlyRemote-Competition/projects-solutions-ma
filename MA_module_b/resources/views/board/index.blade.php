<!DOCTYPE html>
<html>
<head>
    <title>Stations Index</title>
    <style>
        body{
            font-family:sans-serif; 
            padding:2rem;
        } 
        a{
            text-decoration:none; 
            color:blue;
        }
    </style>
</head>
<body>
    <h1>Shanghai Ferry Stations</h1>
    <ul>
        @foreach($stations as $station)
            <li>
                <a href="{{ url('XX_Module_B/board/' . $station->code) }}">
                    {{ $station->code }} - {{ $station->name }}
                </a>
            </li>
        @endforeach
    </ul>
</body>
</html>