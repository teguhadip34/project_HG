<?php

namespace App\Http\Controllers;

use App\Models\EcoProfile;
use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class RewardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $profile = EcoProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['xp' => 0, 'level' => 1]
        );

        return view('hadiah', [
            'balance'     => $profile->balance(),
            'totalXp'     => $profile->xp,
            'isGuest'     => (bool) $user->is_guest,
            'rewards'     => Reward::where('is_active', true)->orderBy('cost_xp')->get(),
            'redemptions' => RewardRedemption::with('reward')
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function redeem(Request $request, Reward $reward)
    {
        $user = $request->user();

        if ($user->is_guest) {
            return back()->with('error', 'Akun Guest belum bisa menukar hadiah. Daftar akun dulu ya.');
        }

        try {
            $redemption = DB::transaction(function () use ($user, $reward) {
                // Kunci baris supaya tidak bisa ditukar ganda lewat klik berulang.
                $profile = EcoProfile::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
                $locked  = Reward::whereKey($reward->id)->lockForUpdate()->firstOrFail();

                if (! $locked->is_active) {
                    throw new RuntimeException('Hadiah ini sedang tidak tersedia.');
                }

                if ($locked->isOutOfStock()) {
                    throw new RuntimeException('Maaf, stok hadiah ini sudah habis.');
                }

                if ($profile->balance() < $locked->cost_xp) {
                    throw new RuntimeException('Saldo XP kamu belum cukup untuk hadiah ini.');
                }

                if ($locked->stock !== null) {
                    $locked->decrement('stock');
                }

                do {
                    $code = 'ECO-' . strtoupper(Str::random(8));
                } while (RewardRedemption::where('code', $code)->exists());

                return RewardRedemption::create([
                    'user_id'   => $user->id,
                    'reward_id' => $locked->id,
                    'cost_xp'   => $locked->cost_xp,
                    'code'      => $code,
                ]);
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Berhasil menukar {$reward->name}! Kode penukaran: {$redemption->code}");
    }
}
