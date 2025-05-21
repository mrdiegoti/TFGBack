<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $date = now()->format('Y/m/d');

        // IMPORTANT: You are hardcoding an API key here which is not secure.
        // It should be fetched from .env like in getGameDetail.
        // For now, I'm just correcting your existing logic, but this is a security risk.
        $client = new \GuzzleHttp\Client();
        $response = $client->get("https://api.sportradar.us/nba/trial/v8/en/games/{$date}/schedule.json", [
            'query' => ['api_key' => env('SPORTRADAR_API_KEY')], // Changed this to use env()
        ]);

        return response()->json(json_decode($response->getBody(), true));
    }

    public function getGameDetail($id)
    {
        try {
            $apiKey = env('SPORTRADAR_API_KEY');
            // Ensure the API key is actually set and not empty
            if (empty($apiKey)) {
                Log::error('SPORTRADAR_API_KEY is not set in .env file.');
                return response()->json(['error' => 'API key not configured.'], 500);
            }

            $url = "https://api.sportradar.us/nba/trial/v8/en/games/{$id}/summary.json?api_key={$apiKey}";
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();

                // --- Extensive Logging for Debugging ---
                // Log the full raw Sportradar response to verify its structure
                Log::info('Sportradar API Raw Response for game ' . $id, ['response_data' => $data]);

                $homeStats = [];
                $awayStats = [];

                // Process Home Team Players
                // The API response shows players directly under 'home' and 'away' objects
                if (isset($data['home']['players']) && is_array($data['home']['players'])) {
                    foreach ($data['home']['players'] as $player) {
                        // Ensure 'statistics' key exists for the player before accessing it
                        if (isset($player['statistics'])) {
                            $stats = [
                                'id' => $player['id'] ?? null,
                                'name' => $player['full_name'] ?? 'N/A',
                                'points' => $player['statistics']['points'] ?? 0,
                                'rebounds' => $player['statistics']['rebounds'] ?? ($player['statistics']['tot_reb'] ?? 0), // Fallback for rebounds
                                'assists' => $player['statistics']['assists'] ?? 0,
                                'steals' => $player['statistics']['steals'] ?? 0,
                                'blocks' => $player['statistics']['blocks'] ?? 0,
                                'personal_fouls' => $player['statistics']['personal_fouls'] ?? 0,
                                'minutes' => $player['statistics']['minutes'] ?? '0:00' // Added minutes for completeness
                            ];
                            $homeStats[] = $stats;
                        } else {
                            Log::warning('Player ' . ($player['full_name'] ?? 'Unknown') . ' has no statistics data in home team.');
                        }
                    }
                } else {
                    Log::info('No players data found for home team in game: ' . $id);
                }

                // Process Away Team Players
                if (isset($data['away']['players']) && is_array($data['away']['players'])) {
                    foreach ($data['away']['players'] as $player) {
                        // Ensure 'statistics' key exists for the player before accessing it
                        if (isset($player['statistics'])) {
                            $stats = [
                                'id' => $player['id'] ?? null,
                                'name' => $player['full_name'] ?? 'N/A',
                                'points' => $player['statistics']['points'] ?? 0,
                                'rebounds' => $player['statistics']['rebounds'] ?? ($player['statistics']['tot_reb'] ?? 0), // Fallback for rebounds
                                'assists' => $player['statistics']['assists'] ?? 0,
                                'steals' => $player['statistics']['steals'] ?? 0,
                                'blocks' => $player['statistics']['blocks'] ?? 0,
                                'personal_fouls' => $player['statistics']['personal_fouls'] ?? 0,
                                'minutes' => $player['statistics']['minutes'] ?? '0:00' // Added minutes for completeness
                            ];
                            $awayStats[] = $stats;
                        } else {
                            Log::warning('Player ' . ($player['full_name'] ?? 'Unknown') . ' has no statistics data in away team.');
                        }
                    }
                } else {
                    Log::info('No players data found for away team in game: ' . $id);
                }

                // --- Sorting the player statistics by minutes in descending order ---
                // Helper function to convert "MM:SS" to total seconds for comparison
                $convertMinutesToSeconds = function ($time) {
                    if (strpos($time, ':') === false) { // Handle cases where minutes might be just a number
                        return (int)$time * 60;
                    }
                    list($minutes, $seconds) = explode(':', $time);
                    return (int)$minutes * 60 + (int)$seconds;
                };

                usort($homeStats, function ($a, $b) use ($convertMinutesToSeconds) {
                    $minutesA = $convertMinutesToSeconds($a['minutes']);
                    $minutesB = $convertMinutesToSeconds($b['minutes']);
                    return $minutesB <=> $minutesA; // Descending order
                });

                usort($awayStats, function ($a, $b) use ($convertMinutesToSeconds) {
                    $minutesA = $convertMinutesToSeconds($a['minutes']);
                    $minutesB = $convertMinutesToSeconds($b['minutes']);
                    return $minutesB <=> $minutesA; // Descending order
                });

                // Log the final aggregated stats before returning
                Log::info('Final Home Stats Count: ' . count($homeStats), ['home_stats_sample' => array_slice($homeStats, 0, 5)]);
                Log::info('Final Away Stats Count: ' . count($awayStats), ['away_stats_sample' => array_slice($awayStats, 0, 5)]);

                // Return the relevant data along with the organized statistics
                return response()->json([
                    'game' => $data['game'] ?? null,
                    'home' => $data['home'] ?? null,
                    'away' => $data['away'] ?? null,
                    'venue' => $data['venue'] ?? null,
                    'status' => $data['status'] ?? null,
                    'scheduled' => $data['scheduled'] ?? null,
                    'summary' => $data['summary'] ?? null,
                    'statistics' => [
                        'home' => $homeStats,
                        'away' => $awayStats,
                    ],
                ]);
            } else {
                Log::error('Sportradar API call failed for game ' . $id, [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'error' => 'Error desde Sportradar',
                    'status' => $response->status(),
                    'body' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::critical('Exception in getGameDetail for game ' . $id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Excepción al conectar con Sportradar',
                'message' => $e->getMessage()
            ], 503);
        }
    }
}