<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Finder</title>
</head>
<body>
    <h1>Find Nearby Restaurants</h1>

    {{-- Error message --}}
    @if(session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif

    {{-- Search Form --}}
    <form action="{{ route('search') }}" method="GET">
    <label for="city">Enter City:</label>
    <input type="text" name="city" placeholder="e.g. Delhi" required>
    <button type="submit">Search</button>
</form>

    {{-- Show Results --}}
    @if(isset($places))
        <h2>Restaurants in {{ $city }}</h2>

        @if(count($places) > 0)
            <ul>
                @foreach($places as $place)
                    <li>
                        {{ $place['tags']['name'] ?? 'Unnamed Restaurant' }}
                        @if(isset($place['tags']['cuisine']))
                            ({{ $place['tags']['cuisine'] }})
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p>No restaurants found in {{ $city }}.</p>
        @endif

        <a href="{{ url('/') }}">Back</a>
    @endif
</body>
</html>