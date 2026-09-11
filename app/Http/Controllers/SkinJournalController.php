<?php

namespace App\Http\Controllers;

use App\Models\SkinJournal;
use App\Models\SkinConcern;
use App\Services\RewardService;
use Illuminate\Http\Request;

class SkinJournalController extends Controller
{
    /**
     * Display a listing of the user's skin journals.
     */
    public function index(Request $request)
    {
        $journals = $request->user()->skinJournals()
            ->orderBy('journal_date', 'desc')
            ->paginate(10);
            
        return view('journal.index', compact('journals'));
    }

    /**
     * Show the form for creating a new journal entry.
     */
    public function create()
    {
        // Check if user already submitted a journal today
        if (auth()->user()->skinJournals()->where('journal_date', now()->toDateString())->exists()) {
            return redirect()->route('journal.index')->with('warning', 'Kamu sudah mengisi jurnal hari ini.');
        }

        $concerns = SkinConcern::all();
        return view('journal.create', compact('concerns'));
    }

    /**
     * Store a newly created journal entry in storage.
     */
    public function store(Request $request, RewardService $rewardService)
    {
        // Prevent multiple entries per day
        if ($request->user()->skinJournals()->where('journal_date', now()->toDateString())->exists()) {
            return redirect()->route('journal.index')->with('warning', 'Kamu sudah mengisi jurnal hari ini.');
        }

        $validated = $request->validate([
            'skin_condition' => 'required|in:excellent,good,moderate,poor,critical',
            'mood' => 'required|in:happy,neutral,stressed,tired,sad',
            'notes' => 'nullable|string|max:1000',
            'concerns' => 'nullable|array',
            'concerns.*' => 'exists:skin_concerns,id'
        ]);

        $journal = SkinJournal::create([
            'user_id' => $request->user()->id,
            'journal_date' => now()->toDateString(),
            'skin_condition' => $validated['skin_condition'],
            'mood' => $validated['mood'],
            'notes' => $validated['notes'],
        ]);

        if (!empty($validated['concerns'])) {
            $journal->concerns()->attach($validated['concerns']);
        }

        // Award points for writing journal
        $rewardService->awardPoints(
            $request->user(),
            5,
            'Daily journal entry',
            'journal',
            $journal->id
        );

        return redirect()->route('journal.index')->with('success', 'Jurnal hari ini berhasil disimpan! Kamu mendapat +5 Points.');
    }
}
