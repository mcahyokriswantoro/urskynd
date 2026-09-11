<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'First Skin Check',
                'description' => 'Selesaikan skin check pertamamu!',
                'icon' => '🔍',
                'requirement_type' => 'skin_check_count',
                'requirement_value' => 1,
                'reward_points' => 20,
            ],
            [
                'name' => 'Skin Explorer',
                'description' => 'Lakukan 5 kali skin check.',
                'icon' => '🧪',
                'requirement_type' => 'skin_check_count',
                'requirement_value' => 5,
                'reward_points' => 50,
            ],
            [
                'name' => 'Skin Scientist',
                'description' => 'Lakukan 10 kali skin check.',
                'icon' => '🔬',
                'requirement_type' => 'skin_check_count',
                'requirement_value' => 10,
                'reward_points' => 100,
            ],
            [
                'name' => '7 Day Streak',
                'description' => 'Isi skin journal selama 7 hari.',
                'icon' => '🔥',
                'requirement_type' => 'streak_days',
                'requirement_value' => 7,
                'reward_points' => 30,
            ],
            [
                'name' => '30 Day Routine',
                'description' => 'Konsisten dengan routine selama 30 hari.',
                'icon' => '💪',
                'requirement_type' => 'routine_days',
                'requirement_value' => 30,
                'reward_points' => 100,
            ],
            [
                'name' => 'Skin Improvement',
                'description' => 'Tingkatkan skin score sebanyak 10 poin.',
                'icon' => '📈',
                'requirement_type' => 'score_improvement',
                'requirement_value' => 10,
                'reward_points' => 50,
            ],
            [
                'name' => 'Journal Master',
                'description' => 'Buat 20 entries di skin journal.',
                'icon' => '📝',
                'requirement_type' => 'journal_count',
                'requirement_value' => 20,
                'reward_points' => 75,
            ],
            [
                'name' => 'Consistency is Beauty',
                'description' => 'Isi skin journal selama 14 hari berturut-turut.',
                'icon' => '⭐',
                'requirement_type' => 'streak_days',
                'requirement_value' => 14,
                'reward_points' => 60,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
