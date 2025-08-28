<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Finder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; }
        .error { color: red; padding: 10px; background: #fee; border: 1px solid #faa; }
        .loading { display: none; }
        form.searching .loading { display: block; }
    </style>
</head>
<body>
    <h1>Find Nearby Restaurants</h1>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form action="{{ route('search') }}" method="GET" onsubmit="this.classList.add('searching')">
        <label for="city">Enter City:</label>
        <input type="text" 
               id="city" 
               name="city" 
               value="{{ old('city') }}" 
               placeholder="e.g. Delhi" 
               required 
               pattern=".{2,100}"
               title="City name must be between 2 and 100 characters">
        <button type="submit">Search</button>
        <div class="loading">Searching...</div>
    </form>

    @if(isset($places))
        <h2>Restaurants in {{ $city }}</h2>
        @forelse($places as $place)
            <div class="restaurant">
                <h3>{{ $place['tags']['name'] ?? 'Unnamed Restaurant' }}</h3>
                @if(isset($place['tags']['cuisine']))
                    <p>Cuisine: {{ $place['tags']['cuisine'] }}</p>
                @endif
                @if(isset($place['tags']['opening_hours']))
                    <p>Hours: {{ $place['tags']['opening_hours'] }}</p>
                @endif
            </div>
        @empty
            <p>No restaurants found in {{ $city }}.</p>
        @endforelse

        <a href="{{ url('/') }}">New Search</a>
    @endif
</body>
</html>