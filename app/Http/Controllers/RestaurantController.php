<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RestaurantController extends Controller
{
    // Index page load
    public function index()
    {
        return view('restaurants');
    }

    // Search by city
   
    public function search(Request $request)
    {
        $city = $request->input('city');
    
        try {
            // 1. Get city coordinates
            $response = Http::withHeaders([
                'User-Agent' => 'MyLaravelApp/1.0 (your_email@example.com)'
            ])->get("https://nominatim.openstreetmap.org/search", [
                'city'   => $city,
                'format' => 'json',
                'limit'  => 1
            ]);
    
            $data = $response->json();
    
            if (empty($data)) {
                return back()->with('error', 'City not found!');
            }
    
            $lat = $data[0]['lat'];
            $lon = $data[0]['lon'];
    
            // 2. Get restaurants
            $overpassUrl = "https://overpass-api.de/api/interpreter";
            $query = "[out:json];(node[amenity=restaurant](around:5000,{$lat},{$lon}););out body;";
    
            $places = Http::withHeaders([
                'User-Agent' => 'MyLaravelApp/1.0 (your_email@example.com)'
            ])->get($overpassUrl, [
                'data' => $query
            ])->json();
    
            return view('restaurants', [
                'city'   => $city,
                'places' => $places['elements'] ?? []
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while searching for restaurants.');
        }
    }
}
