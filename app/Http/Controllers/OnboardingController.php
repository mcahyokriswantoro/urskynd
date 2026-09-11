<?php

namespace App\Http\Controllers;

use App\Models\SkinConcern;
use App\Models\SkinType;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding form.
     */
    public function index(Request $request)
    {
        // If already completed, redirect to dashboard
        if ($request->user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        $skinTypes = SkinType::all();
        $skinConcerns = SkinConcern::all();

        return view('onboarding.index', compact('skinTypes', 'skinConcerns'));
    }

    /**
     * Process the onboarding data.
     */
    public function store(Request $request)
    {
        if ($request->user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'skin_type_id' => 'required|exists:skin_types,id',
            'concerns' => 'nullable|array',
            'concerns.*' => 'exists:skin_concerns,id',
            'skin_goal' => 'nullable|string|max:255',
            'allergies' => 'nullable|string|max:255',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:female,male,other',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $user = $request->user();

            // Update User basics
            $user->update([
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'onboarding_completed' => true,
            ]);

            // Create Profile
            UserProfile::create([
                'user_id' => $user->id,
                'skin_type_id' => $validated['skin_type_id'],
                'skin_goal' => $validated['skin_goal'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
            ]);

            // Attach Concerns
            if (!empty($validated['concerns'])) {
                $concernsData = [];
                foreach ($validated['concerns'] as $concernId) {
                    $concernsData[$concernId] = ['severity' => 'moderate'];
                }
                $user->skinConcerns()->sync($concernsData);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Selamat datang di URSKYND! Profil kulitmu berhasil disimpan.');
    }
}
