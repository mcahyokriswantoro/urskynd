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
        $user->load('achievements');
        
        // Get point history
        $pointHistory = $user->pointTransactions()->orderBy('created_at', 'desc')->paginate(10);
        
        // Get all available achievements for UI
        $allAchievements = \App\Models\Achievement::all();

        return view('rewards.index', compact('user', 'pointHistory', 'allAchievements'));
    }
}
