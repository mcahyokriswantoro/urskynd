<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SkinAnalysis;
use App\Models\SkinJournal;
use App\Models\SkinType;
use App\Models\SkinConcern;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the comprehensive admin dashboard.
     */
    public function index()
    {
        // 1. KPI Statistics
        $totalUsers = User::where('role', 'user')->count();
        $totalAnalyses = SkinAnalysis::count();
        $totalProducts = Product::count();
        $totalJournals = SkinJournal::count();
        $totalPoints = PointTransaction::where('type', 'credit')->sum('points');
        $avgSkinScore = round(SkinAnalysis::avg('overall_score') ?? 0, 1);

        // 2. Skin Type Distribution
        $skinTypes = SkinType::withCount('users')->get();

        // 3. Most common skin concerns
        $skinConcerns = SkinConcern::withCount('users')->orderBy('users_count', 'desc')->take(5)->get();

        // 4. Recent Users (Paginated / Top 8)
        $recentUsers = User::where('role', 'user')
            ->with(['profile.skinType', 'latestAnalysis'])
            ->latest()
            ->take(8)
            ->get();

        // 5. Recent Skin Analyses
        $recentAnalyses = SkinAnalysis::with(['user', 'skinType', 'metrics'])
            ->latest('analysis_date')
            ->take(6)
            ->get();

        // 6. Recent Products
        $products = Product::with('category')->latest()->take(6)->get();

        // 7. Recent Point Transactions
        $recentPoints = PointTransaction::with('user')->latest()->take(6)->get();

        // 8. 7-Day Activity Trends
        $days = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i);
            return [
                'date' => $date->format('d M'),
                'raw_date' => $date->toDateString(),
                'analyses' => SkinAnalysis::whereDate('created_at', $date->toDateString())->count(),
                'journals' => SkinJournal::whereDate('created_at', $date->toDateString())->count(),
            ];
        });

        $chartDates = $days->pluck('date')->toArray();
        $chartAnalyses = $days->pluck('analyses')->toArray();
        $chartJournals = $days->pluck('journals')->toArray();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAnalyses',
            'totalProducts',
            'totalJournals',
            'totalPoints',
            'avgSkinScore',
            'skinTypes',
            'skinConcerns',
            'recentUsers',
            'recentAnalyses',
            'products',
            'recentPoints',
            'chartDates',
            'chartAnalyses',
            'chartJournals'
        ));
    }
}
