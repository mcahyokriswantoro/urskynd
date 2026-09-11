<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\SkinAnalysis;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'total_analyses' => SkinAnalysis::count(),
            'recent_users' => User::where('role', 'user')->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
