<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RestaurantController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour
    
    public function search(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'city' => 'required|string|min:2|max:100'
        ]);
        
        $city = $validated['city'];
        
        try {
            // Try to get from cache first
            $cacheKey = "restaurants_{$city}";
            if (Cache::has($cacheKey)) {
                return view('restaurants', Cache::get($cacheKey));
            }

            // 1. Get city coordinates with timeout and retry
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => config('app.name') . '/1.0',
                    'Accept' => 'application/json'
                ])
                ->get("https://nominatim.openstreetmap.org/search", [
                    'city' => $city,
                    'format' => 'json',
                    'limit' => 1
                ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch city coordinates');
            }

            $data = $response->json();

            if (empty($data)) {
                return back()->with('error', 'City not found!');
            }

            $lat = $data[0]['lat'];
            $lon = $data[0]['lon'];

            // 2. Get restaurants with timeout
            $overpassUrl = "https://overpass-api.de/api/interpreter";
            $query = "[out:json];(node[amenity=restaurant](around:5000,{$lat},{$lon}););out body;";

            $places = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => config('app.name') . '/1.0',
                    'Accept' => 'application/json'
                ])
                ->get($overpassUrl, [
                    'data' => $query
                ]);

            if (!$places->successful()) {
                throw new \Exception('Failed to fetch restaurants');
            }

            $result = [
                'city' => $city,
                'places' => $places->json()['elements'] ?? []
            ];

            // Cache the results
            Cache::put($cacheKey, $result, self::CACHE_TTL);

            return view('restaurants', $result);
            
        } catch (\Exception $e) {
            \Log::error('Restaurant search failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while searching for restaurants. Please try again later.');
        }
    }
}