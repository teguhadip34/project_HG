<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\EcoProfile;
use App\Models\Scan;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    public function levelFor(int $xp): int
    {
        $level = 1;

        foreach (config('gamification.levels') as $number => $def) {
            if ($xp >= $def['xp']) {
                $level = $number;
            }
        }

        return $level;
    }

    public function progress(EcoProfile $profile): array
    {
        $levels = config('gamification.levels');
        $level = $this->levelFor($profile->xp);
        $current = $levels[$level];
        $next = $levels[$level + 1] ?? null;

        if ($next) {
            $span = $next['xp'] - $current['xp'];
            $into = $profile->xp - $current['xp'];
            $pct = (int) round($into / $span * 100);
        } else {
            $span = 0;
            $into = 0;
            $pct = 100;
        }

        return [
            'level' => $level,
            'name' => $current['name'],
            'icon' => $current['icon'],
            'xp' => $profile->xp,
            'into' => $into,
            'span' => $span,
            'pct' => $pct,
            'is_max' => $next === null,
            'next_name' => $next['name'] ?? null,
            'next_icon' => $next['icon'] ?? null,
            'xp_to_next' => $next ? $next['xp'] - $profile->xp : 0,
        ];
    }

    public function awardScan(Scan $scan, bool $duplicate = false): array
    {
        $profile = EcoProfile::where('user_id', $scan->user_id)
            ->firstOrFail();

        $result = [
            'duplicate' => $duplicate,
            'xp_gained' => 0,
            'breakdown' => [],
            'achievements' => [],
            'achievement_xp' => 0,
            'challenges' => [],
            'challenge_xp' => 0,
            'leveled_up' => false,
            'progress' => $this->progress($profile),
        ];

        if (!$scan->waste_type_id || $duplicate) {
            return $result;
        }

        return DB::transaction(function () use ($scan, $result) {
            $cfg = config('gamification.xp');

            $profile = EcoProfile::where('user_id', $scan->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldLevel = $this->levelFor($profile->xp);

            $breakdown = [
                ['label' => 'Scan berhasil dikenali', 'xp' => $cfg['scan']]
            ];

            if ((float) $scan->confidence >= $cfg['high_confidence_threshold']) {
                $breakdown[] = [
                    'label' => 'Keyakinan AI tinggi',
                    'xp' => $cfg['high_confidence']
                ];
            }

            $seenBefore = Scan::where('user_id', $scan->user_id)
                ->where('waste_type_id', $scan->waste_type_id)
                ->where('xp_earned', '>', 0)
                ->where('id', '<', $scan->id)
                ->exists();

            if (!$seenBefore) {
                $breakdown[] = [
                    'label' => 'Jenis sampah baru',
                    'xp' => $cfg['new_type']
                ];
            }

            $scanXp = array_sum(array_column($breakdown, 'xp'));

            $scan->update(['xp_earned' => $scanXp]);

            $unlocked = $this->unlockAchievements(
                $scan->user_id,
                $scan->created_at ?? now()
            );

            $achievementXp = array_sum(
                array_column($unlocked, 'xp_reward')
            );

            $completed = $this->completeChallenges($scan);

            $challengeXp = array_sum(
                array_column($completed, 'xp_reward')
            );

            $profile->xp += $scanXp + $achievementXp + $challengeXp;
            $profile->level = $this->levelFor($profile->xp);
            $profile->save();

            $result['xp_gained'] = $scanXp;
            $result['breakdown'] = $breakdown;
            $result['achievements'] = $unlocked;
            $result['achievement_xp'] = $achievementXp;
            $result['challenges'] = $completed;
            $result['challenge_xp'] = $challengeXp;
            $result['leveled_up'] = $profile->level > $oldLevel;
            $result['progress'] = $this->progress($profile);

            return $result;
        });
    }


public function metrics(?int $userId = null): array
{
    $userId ??= auth()->id();

    if (!$userId) {
        throw new \RuntimeException('Pengguna belum terautentikasi.');
    }

    $earning = fn () => Scan::where('user_id', $userId)
        ->where('xp_earned', '>', 0);

    $dates = $earning()->pluck('created_at')
        ->map(fn ($date) => $date->toDateString())
        ->unique()
        ->sort()
        ->values();

    return [
        'scans' => $earning()->count(),

        'distinct_types' => $earning()
            ->distinct()
            ->count('waste_type_id'),

        'anorganik_scans' => $earning()
            ->whereHas(
                'wasteType.category',
                fn ($q) => $q->where('name', 'Anorganik')
            )
            ->count(),

        'lainnya_scans' => $earning()
            ->whereHas(
                'wasteType.category',
                fn ($q) => $q->where('name', 'Lainnya')
            )
            ->count(),

        'streak_days' => $this->longestStreak($dates),
    ];
}


    private function unlockAchievements(int $userId, $when): array
    {
        $metrics = $this->metrics($userId);
        $unlocked = [];

        $alreadyUnlocked = DB::table('user_achievements')
            ->where('user_id', $userId)
            ->pluck('achievement_id')
            ->all();

        foreach (Achievement::orderBy('id')->get() as $achievement) {
            if (in_array($achievement->id, $alreadyUnlocked)) {
                continue;
            }

            if (($metrics[$achievement->metric] ?? 0) >= $achievement->target) {
                DB::table('user_achievements')->insert([
                    'user_id' => $userId,
                    'achievement_id' => $achievement->id,
                    'unlocked_at' => $when,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $unlocked[] = [
                    'name' => $achievement->name,
                    'icon' => $achievement->icon,
                    'description' => $achievement->description,
                    'xp_reward' => $achievement->xp_reward,
                ];
            }
        }

        return $unlocked;
    }

    /** Daftar tantangan beserta progres periode berjalan (untuk halaman Tantangan). */
    public function challenges(int $userId): array
    {
        $out = ['daily' => [], 'weekly' => []];

        foreach (config('gamification.challenges', []) as $def) {
            $start   = $this->periodStart($def['period'], now());
            $end     = $this->periodEnd($def['period'], $start);
            $current = min($this->challengeValue($def, $userId, $start), $def['target']);

            $out[$def['period']][] = $def + [
                'current'   => $current,
                'pct'       => $def['target'] > 0 ? (int) round($current / $def['target'] * 100) : 0,
                'done'      => $current >= $def['target'],
                'remaining' => $this->remaining($end),
            ];
        }

        return $out;
    }

    /** Tandai tantangan yang selesai karena scan ini dan kembalikan yang baru selesai. */
    private function completeChallenges(Scan $scan): array
    {
        $when = Carbon::parse($scan->created_at ?? now());
        $done = [];

        foreach (config('gamification.challenges', []) as $def) {
            $start = $this->periodStart($def['period'], $when);

            if ($this->challengeValue($def, $scan->user_id, $start, $scan->id) < $def['target']) {
                continue;
            }

            // Unique (user, tantangan, periode): hadiah hanya sekali per periode.
            $inserted = DB::table('challenge_completions')->insertOrIgnore([
                'user_id'        => $scan->user_id,
                'challenge_code' => $def['code'],
                'period_start'   => $start->toDateString(),
                'xp_reward'      => $def['xp_reward'],
                'completed_at'   => $when,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            if ($inserted) {
                $done[] = [
                    'code'      => $def['code'],
                    'name'      => $def['name'],
                    'icon'      => $def['icon'],
                    'xp_reward' => $def['xp_reward'],
                ];
            }
        }

        return $done;
    }

    private function periodStart(string $period, \Carbon\CarbonInterface $when): Carbon
    {
        $copy = Carbon::parse($when);

        return $period === 'weekly' ? $copy->startOfWeek() : $copy->startOfDay();
    }

    private function periodEnd(string $period, Carbon $start): Carbon
    {
        return $period === 'weekly' ? $start->copy()->addWeek() : $start->copy()->addDay();
    }

    private function challengeValue(array $def, int $userId, Carbon $start, ?int $upToScanId = null): int
    {
        $query = Scan::where('user_id', $userId)
            ->where('xp_earned', '>', 0)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $this->periodEnd($def['period'], $start));

        if ($upToScanId !== null) {
            $query->where('id', '<=', $upToScanId);
        }

        return $def['metric'] === 'distinct_types'
            ? (int) $query->distinct()->count('waste_type_id')
            : (int) $query->count();
    }

    private function remaining(Carbon $end): string
    {
        $mins = max(0, (int) now()->diffInMinutes($end));
        $d = intdiv($mins, 1440);
        $h = intdiv($mins % 1440, 60);
        $m = $mins % 60;

        if ($d > 0) {
            return "{$d} hari {$h} jam";
        }

        return $h > 0 ? "{$h} jam {$m} menit" : "{$m} menit";
    }

    /** Streak aktif: hari berturut-turut sampai hari ini (atau kemarin, jika hari ini belum scan). */
    public function currentStreak(int $userId): int
    {
        $dates = Scan::where('user_id', $userId)
            ->where('xp_earned', '>', 0)
            ->pluck('created_at')
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->flip();

        $cursor = Carbon::today();

        if (! isset($dates[$cursor->toDateString()])) {
            $cursor = $cursor->subDay();
        }

        $streak = 0;

        while (isset($dates[$cursor->toDateString()])) {
            $streak++;
            $cursor = $cursor->subDay();
        }

        return $streak;
    }

    private function longestStreak(Collection $dates): int
    {
        $best = 0;
        $run = 0;
        $prev = null;

        foreach ($dates as $date) {
            $day = Carbon::parse($date);

            $run = ($prev && $prev->copy()->addDay()->isSameDay($day))
                ? $run + 1
                : 1;

            $best = max($best, $run);
            $prev = $day;
        }

        return $best;
    }
}

