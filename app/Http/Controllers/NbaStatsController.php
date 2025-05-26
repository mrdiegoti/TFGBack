<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NbaStatsController extends Controller
{
    public function standings()
    {
        $response = Http::get("https://api.sportradar.us/nba/trial/v8/en/seasons/2023/REG/standings.json", [
            'api_key' => env('SPORTRADAR_API_KEY')
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Error al obtener standings'], 500);
        }

        $data = $response->json();

        return response()->json($data['conferences']);
    }

    // public function teamStats($teamId)
    // {
    //     $apiKey = env('SPORTRADAR_API_KEY');
    //     Log::info("teamStats recibido: " . $teamId);

    //     // Obtener perfil del equipo con roster
    //     $profileResponse = Http::get("https://api.sportradar.us/nba/trial/v8/en/seasons/2023/REG/teams/{$teamId}/profile.json", [
    //         'api_key' => $apiKey
    //     ]);

    //     if (!$profileResponse->successful()) {
    //         Log::error("Error API Sportradar: HTTP " . $profileResponse->status());
    //         Log::error($profileResponse->body());
    //         return response()->json(['error' => 'Error al obtener perfil del equipo'], 500);
    //     }

    //     $profileData = $profileResponse->json();

    //     if (!isset($profileData['players']) || empty($profileData['players'])) {
    //         return response()->json(['error' => 'No se encontraron jugadores en el equipo'], 404);
    //     }

    //     $playersStats = [];

    //     // Limitar a 6 jugadores
    //     $playersToProcess = array_slice($profileData['players'], 0, 6);

    //     foreach ($playersToProcess as $player) {
    //         $playerId = $player['id'] ?? null;

    //         if (!$playerId) {
    //             Log::warning("Jugador sin ID válido, saltando");
    //             continue;
    //         }

    //         // Obtener estadísticas individuales del jugador
    //         $statsResponse = Http::get("https://api.sportradar.us/nba/trial/v8/en/players/{$playerId}/statistics.json", [
    //             'api_key' => $apiKey
    //         ]);

    //         if (!$statsResponse->successful()) {
    //             Log::warning("No se pudo obtener estadísticas para jugador {$playerId} - HTTP " . $statsResponse->status());
    //             continue;
    //         }

    //         $statsData = $statsResponse->json();

    //         $average = $statsData['average'] ?? [];

    //         $playersStats[] = [
    //             'name' => $player['full_name'] ?? 'Desconocido',
    //             'points' => $average['points'] ?? null,
    //             'rebounds' => $average['rebounds'] ?? null,
    //             'assists' => $average['assists'] ?? null,
    //             'steals' => $average['steals'] ?? null,
    //             'blocks' => $average['blocks'] ?? null,
    //             'fg_pct' => $average['field_goals_pct'] ?? null,
    //             'three_pt_pct' => $average['three_point_pct'] ?? null,
    //         ];
    //     }

    //     return response()->json($playersStats);
    // }
}
