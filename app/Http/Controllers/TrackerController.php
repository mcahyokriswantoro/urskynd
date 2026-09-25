<?php

namespace App\Http\Controllers;

use App\Services\SkinAnalysisService;
use Illuminate\Http\Request;

class TrackerController extends Controller
{
    /**
     * Display the skin progress tracker.
     */
    public function index(Request $request, SkinAnalysisService $analysisService)
    {
        $period = $request->query('period', 'monthly');
        $user = $request->user();

        // Get progress chart data
        $progressData = $analysisService->getProgressData($user, $period, 12);

        // Get historical analyses paginated for history list
        $analyses = $user->skinAnalyses()
            ->with(['metrics', 'skinType'])
            ->orderBy('analysis_date', 'desc')
            ->paginate(6);

        // Calculate improvement stats
        $latestAnalysis = $user->skinAnalyses()->latest('analysis_date')->first();
        $oldestAnalysis = $user->skinAnalyses()->oldest('analysis_date')->first();

        $scoreDiff = 0;
        $skinAgeDiff = 0;

        if ($latestAnalysis && $oldestAnalysis && $latestAnalysis->id !== $oldestAnalysis->id) {
            $scoreDiff = $latestAnalysis->overall_score - $oldestAnalysis->overall_score;
            $skinAgeDiff = round($latestAnalysis->estimated_skin_age - $oldestAnalysis->estimated_skin_age, 1);
        }

        return view('tracker.index', compact(
            'progressData',
            'analyses',
            'latestAnalysis',
            'scoreDiff',
            'skinAgeDiff',
            'period'
        ));
    }
}
