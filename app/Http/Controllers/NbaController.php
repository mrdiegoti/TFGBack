<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NbaController extends Controller
{
    public function getSchedule($date)
    {
        $apiKey = 'wfVIBOSY7GR3c12XXhUnzewMqMMr3Skgitqkqafh';
        $url = "https://api.sportradar.us/nba/trial/v8/en/games/{$date}/schedule.json?api_key={$apiKey}";

        $response = Http::get($url);

        // Puedes añadir control de errores si quieres
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Error al obtener los datos'], $response->status());
        }
    }

    public function calendario(Request $request)
{
    $date = $request->input('date', '2024/11/01');

    $client = new \GuzzleHttp\Client();
    $response = $client->get("https://api.sportradar.us/nba/trial/v8/en/games/{$date}/schedule.json", [
        'query' => ['api_key' => env('wfVIBOSY7GR3c12XXhUnzewMqMMr3Skgitqkqafh')],
    ]);

    return response()->json(json_decode($response->getBody(), true));
}
}
