<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\EcoProfile;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request, GamificationService $gamification)
    {
        $user = $request->user();

        $profile = EcoProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['xp' => 0, 'level' => 1]
        );

        return view('profil', [
            'user'              => $user,
            'eco'               => $gamification->progress($profile),
            'metrics'           => $gamification->metrics($user->id),
            'currentStreak'     => $gamification->currentStreak($user->id),
            'totalScans'        => $user->scans()->count(),
            'badges'            => $user->achievements()->orderByDesc('user_achievements.unlocked_at')->get(),
            'totalAchievements' => Achievement::count(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
