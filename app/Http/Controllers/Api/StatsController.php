<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    public function __invoke(Request $request, Stats $stats): JsonResponse
    {
        Log::info('API: /status');

        return response()->json([
            'package_count' => $stats->packageCount(),
            'packagist_download_count' => $stats->packagistDownloadsCount(),
            'github_star_count' => $stats->githubStarsCount(),
            'nova_latest_version' => $stats->novaLatestVersion(),
            'collaborator_count' => $stats->collaboratorsCount(),
            'rating_count' => $stats->ratingsCount(),
            'average_rating' => $stats->globalAverageRating(),
        ]);
    }
}
