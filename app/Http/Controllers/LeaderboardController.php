<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    private const PERIODS = [
        'harian'   => 'Hari Ini',
        'mingguan' => 'Minggu Ini',
        'bulanan'  => 'Bulan Ini',
        'semua'    => 'Sepanjang Waktu',
    ];

    private const LIMIT = 20;

    public function index(Request $request)
    {
        $period = $request->query('periode', 'mingguan');
        $period = array_key_exists($period, self::PERIODS) ? $period : 'mingguan';

        $metric = $request->query('urut') === 'xp' ? 'xp' : 'scan';

        $from = match ($period) {
            'harian'   => now()->startOfDay(),
            'mingguan' => now()->startOfWeek(),
            'bulanan'  => now()->startOfMonth(),
            default    => null,
        };

        // Hanya scan yang menghasilkan XP (scan duplikat/tidak dikenali tidak dihitung).
        $rows = User::query()
            ->select('users.id', 'users.name', 'users.is_guest')
            ->selectRaw('COUNT(scans.id) as scan_count')
            ->selectRaw('COALESCE(SUM(scans.xp_earned), 0) as period_xp')
            ->join('scans', 'scans.user_id', '=', 'users.id')
            ->where('scans.xp_earned', '>', 0)
            ->when($from, fn ($q) => $q->where('scans.created_at', '>=', $from))
            ->groupBy('users.id', 'users.name', 'users.is_guest')
            ->orderByDesc($metric === 'xp' ? 'period_xp' : 'scan_count')
            ->orderByDesc($metric === 'xp' ? 'scan_count' : 'period_xp')
            ->orderBy('users.id')
            ->get();

        // Peringkat (nilai sama = peringkat sama)
        $key  = $metric === 'xp' ? 'period_xp' : 'scan_count';
        $prev = null;
        $rank = 0;

        foreach ($rows as $i => $row) {
            if ($row->{$key} !== $prev) {
                $rank = $i + 1;
                $prev = $row->{$key};
            }
            $row->rank = $rank;
        }

        $me    = $rows->firstWhere('id', $request->user()->id);
        $top   = $rows->take(self::LIMIT);
        $meOut = $me && $me->rank > $top->last()?->rank && ! $top->contains('id', $me->id) ? $me : null;

        return view('leaderboard', [
            'rows'    => $top,
            'me'      => $meOut,
            'myId'    => $request->user()->id,
            'periods' => self::PERIODS,
            'period'  => $period,
            'metric'  => $metric,
        ]);
    }
}
