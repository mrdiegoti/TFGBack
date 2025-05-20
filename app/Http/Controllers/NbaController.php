<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NbaController extends Controller
{
    public function getSchedule($date)
    {
        try {
            $formattedDate = str_replace('-', '/', $date);
            $apiKey = env('SPORTRADAR_API_KEY');
            $url = "https://api.sportradar.us/nba/trial/v8/en/games/{$formattedDate}/schedule.json?api_key={$apiKey}";

            $response = Http::get($url);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Error desde Sportradar',
                    'status' => $response->status(),
                    'body' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Excepción al conectar con Sportradar',
                'message' => $e->getMessage()
            ], 503);
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
