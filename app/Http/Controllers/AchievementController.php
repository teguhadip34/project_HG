<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\EcoProfile;
use App\Services\GamificationService;
use Carbon\Carbon;

class AchievementController extends Controller
{
    public function index(GamificationService $gamification)
    {
        $user    = auth()->user();
        $metrics = $gamification->metrics($user->id);

        // id achievement => waktu terbuka (khusus user ini)
        $unlockedAt = $user->achievements()
            ->pluck('user_achievements.unlocked_at', 'achievements.id');

        $achievements = Achievement::orderBy('id')->get()->map(function ($a) use ($metrics, $unlockedAt) {
            $when    = isset($unlockedAt[$a->id]) ? Carbon::parse($unlockedAt[$a->id]) : null;
            $current = $when ? $a->target : min($metrics[$a->metric] ?? 0, $a->target);

            return [
                'achievement' => $a,
                'unlocked_at' => $when,
                'current'     => $current,
                'pct'         => $a->target > 0 ? (int) round($current / $a->target * 100) : 0,
            ];
        });

        $eco = $gamification->progress(EcoProfile::current());

        return view('pencapaian', [
            'achievements' => $achievements,
            'eco'          => $eco,
            'unlocked'     => $unlockedAt->count(),
        ]);
    }
}
