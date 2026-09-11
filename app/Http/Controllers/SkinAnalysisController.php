<?php

namespace App\Http\Controllers;

use App\Models\SkinAnalysis;
use App\Services\SkinAnalysisService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SkinAnalysisController extends Controller
{
    /**
     * Show the skin check landing/upload page.
     */
    public function index()
    {
        return view('analysis.index');
    }

    /**
     * Handle the image upload and start processing.
     */
    public function store(Request $request, SkinAnalysisService $analysisService, RecommendationService $recommendationService)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        // Store the uploaded image
        $path = $request->file('photo')->store('skin_images', 'public');

        try {
            // Perform analysis
            $analysis = $analysisService->analyze($request->user(), $path);
            
            // Generate recommendations based on the new analysis
            $recommendationService->generateRecommendations($request->user(), $analysis);

            // Redirect to the scanning animation, passing the analysis ID
            return redirect()->route('analysis.process', $analysis->id);
            
        } catch (\Exception $e) {
            // If analysis fails, delete the image and show error
            Storage::disk('public')->delete($path);
            return back()->with('error', 'Gagal memproses gambar. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the scanning animation (simulated delay).
     */
    public function process(SkinAnalysis $analysis)
    {
        // Ensure user owns this analysis
        if ($analysis->user_id !== auth()->id()) {
            abort(403);
        }

        return view('analysis.process', compact('analysis'));
    }

    /**
     * Show the complete analysis results.
     */
    public function show(SkinAnalysis $analysis)
    {
        // Ensure user owns this analysis
        if ($analysis->user_id !== auth()->id()) {
            abort(403);
        }

        $analysis->load(['metrics', 'skinType', 'recommendation.items.product']);

        return view('analysis.show', compact('analysis'));
    }
}
