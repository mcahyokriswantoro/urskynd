<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $user->load(['profile.skinType', 'latestAnalysis', 'userProducts.product']);

        $latestAnalysis = $user->latestAnalysis;
        $activeProducts = $user->userProducts()->where('status', 'active')->take(3)->get();
        
        // Determine current routine based on time (before 15:00 = morning, after = night)
        $isMorning = now()->format('H') < 15;
        $routineType = $isMorning ? 'morning' : 'night';
        
        $todayRoutine = $user->routines()
            ->where('routine_type', $routineType)
            ->where('active', true)
            ->with(['items.userProduct.product'])
            ->first();

        // Get progress data for chart
        $progressData = app(\App\Services\SkinAnalysisService::class)->getProgressData($user, 'monthly', 6);

        return view('dashboard', [
            'user' => $user,
            'latestAnalysis' => $latestAnalysis,
            'activeProducts' => $activeProducts,
            'todayRoutine' => $todayRoutine,
            'isMorning' => $isMorning,
            'progressData' => $progressData,
        ]);
    }
}
