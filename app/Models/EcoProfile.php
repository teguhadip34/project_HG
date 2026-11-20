<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcoProfile extends Model
{
    protected $fillable = ['user_id', 'xp', 'level'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function current(): self
    {
        if (! auth()->check()) {
            throw new \RuntimeException('Pengguna belum login.');
        }

        return static::firstOrCreate(
            ['user_id' => auth()->id()],
            ['xp' => 0, 'level' => 1]
        );
    }

    /**
     * Saldo XP yang bisa ditukar = total XP - XP yang sudah dipakai untuk hadiah.
     * Total XP (xp) tidak berkurang, jadi level dan peringkat tetap aman.
     */
    public function balance(): int
    {
        $spent = RewardRedemption::where('user_id', $this->user_id)->sum('cost_xp');

        return max(0, (int) $this->xp - (int) $spent);
    }
}
