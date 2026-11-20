<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'cost_xp', 'stock', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function isOutOfStock(): bool
    {
        return $this->stock !== null && $this->stock <= 0;
    }
}
