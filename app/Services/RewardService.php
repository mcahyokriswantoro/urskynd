<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\DB;

class RewardService
{
    /**
     * Award points to a user.
     */
    public function awardPoints(
        User $user,
        int $points,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): PointTransaction {
        return DB::transaction(function () use ($user, $points, $description, $referenceType, $referenceId) {
            $transaction = PointTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'points' => $points,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            $user->increment('points', $points);

            // Check for achievements
            $this->checkAchievements($user);

            return $transaction;
        });
    }

    /**
     * Deduct points from a user.
     */
    public function deductPoints(
        User $user,
        int $points,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): ?PointTransaction {
        if ($user->points < $points) {
            return null;
        }

        return DB::transaction(function () use ($user, $points, $description, $referenceType, $referenceId) {
            $transaction = PointTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'points' => $points,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            $user->decrement('points', $points);

            return $transaction;
        });
    }

    /**
     * Check and award achievements.
     */
    public function checkAchievements(User $user): void
    {
        $achievements = \App\Models\Achievement::all();

        foreach ($achievements as $achievement) {
            // Skip if already earned
            if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            $qualified = match ($achievement->requirement_type) {
                'skin_check_count' => $user->skinAnalyses()->count() >= $achievement->requirement_value,
                'journal_count' => $user->skinJournals()->count() >= $achievement->requirement_value,
                'routine_days' => $this->getRoutineStreak($user) >= $achievement->requirement_value,
                'streak_days' => $this->getRoutineStreak($user) >= $achievement->requirement_value,
                'score_improvement' => $this->getScoreImprovement($user) >= $achievement->requirement_value,
                default => false,
            };

            if ($qualified) {
                $user->achievements()->attach($achievement->id, [
                    'achieved_at' => now(),
                ]);

                // Award achievement bonus points
                if ($achievement->reward_points > 0) {
                    PointTransaction::create([
                        'user_id' => $user->id,
                        'type' => 'credit',
                        'points' => $achievement->reward_points,
                        'description' => "Achievement: {$achievement->name}",
                        'reference_type' => 'achievement',
                        'reference_id' => $achievement->id,
                    ]);
                    $user->increment('points', $achievement->reward_points);
                }
            }
        }
    }

    private function getRoutineStreak(User $user): int
    {
        // Simple streak calculation based on journal entries
        return $user->skinJournals()
            ->where('journal_date', '>=', now()->subDays(30))
            ->count();
    }

    private function getScoreImprovement(User $user): int
    {
        $analyses = $user->skinAnalyses()->orderBy('analysis_date')->take(2)->get();
        if ($analyses->count() < 2) return 0;
        return max(0, $analyses->last()->overall_score - $analyses->first()->overall_score);
    }
}
