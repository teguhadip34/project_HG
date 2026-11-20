<?php

namespace App\Http\Controllers;

use App\Models\EcoProfile;
use App\Models\Scan;
use App\Services\GamificationService;

class DashboardController extends Controller
{
    public function index(GamificationService $gamification)
    {
        $user = auth()->user();

        $mine = fn () => Scan::where('user_id', $user->id);

        $hitung = fn ($kategori) => $mine()->whereHas(
            'wasteType.category',
            fn ($q) => $q->where('name', $kategori)
        )->count();

        $stats = [
            'total'     => $mine()->count(),
            'organik'   => $hitung('Organik'),
            'anorganik' => $hitung('Anorganik'),
            'lainnya'   => $hitung('Lainnya'),
        ];

        $recent = $mine()->with('wasteType.category')->latest()->take(5)->get()
            ->map(fn ($s) => [
                'nama'     => $s->wasteType->name ?? 'Tidak dikenali',
                'kategori' => $s->wasteType->category->name ?? '-',
            ]);

        $eco = $gamification->progress(EcoProfile::current());

        $achievementsUnlocked = $user->achievements()->count();
        $achievementsTotal    = \App\Models\Achievement::count();

        return view('dashboard', compact('stats', 'recent', 'eco', 'achievementsUnlocked', 'achievementsTotal'));
    }
}
