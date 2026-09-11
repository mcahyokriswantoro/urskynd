<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RewardController extends Controller
{
    /**
     * Display user's rewards, points, and achievements.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Eager load achievements
        $user->load(['achievements.achievement']);
        
        // Get point history
        $pointHistory = $user->points()->orderBy('created_at', 'desc')->paginate(10);
        
        // Get all available achievements for UI
        $allAchievements = \App\Models\Achievement::all();

        return view('rewards.index', compact('user', 'pointHistory', 'allAchievements'));
    }
}
