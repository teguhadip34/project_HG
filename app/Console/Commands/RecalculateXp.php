<?php

namespace App\Console\Commands;

use App\Models\EcoProfile;
use App\Models\Scan;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecalculateXp extends Command
{
    protected $signature = 'ecoscan:recalculate-xp';

    protected $description = 'Hitung ulang XP, level, dan achievement semua user dari scan yang sudah ada';

    public function handle(GamificationService $gamification): int
    {
        DB::transaction(function () use ($gamification) {
            User::query()->each(fn ($u) => EcoProfile::firstOrCreate(
                ['user_id' => $u->id],
                ['xp' => 0, 'level' => 1]
            ));

            DB::table('user_achievements')->delete();
            DB::table('challenge_completions')->delete();
            EcoProfile::query()->update(['xp' => 0, 'level' => 1]);
            Scan::query()->update(['xp_earned' => 0]);

            $seen = [];

            foreach (Scan::orderBy('id')->get() as $scan) {
                $hash = $scan->image_hash ?: $this->hashOf($scan);
                $key  = $scan->user_id . ':' . $hash;
                $duplicate = $hash !== null && isset($seen[$key]);

                if ($hash !== null) {
                    $seen[$key] = true;

                    if ($scan->image_hash !== $hash) {
                        $scan->update(['image_hash' => $hash]);
                    }
                }

                $gamification->awardScan($scan, $duplicate);
            }
        });

        $this->info('Selesai. XP, level, dan achievement semua user sudah dihitung ulang.');

        return self::SUCCESS;
    }

    private function hashOf(Scan $scan): ?string
    {
        $path = Storage::disk('public')->path($scan->image_path);

        return is_file($path) ? sha1_file($path) : null;
    }
}
