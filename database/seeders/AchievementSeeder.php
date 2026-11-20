<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class AchievementSeeder extends Seeder
{
    /** Sinkronkan daftar achievement dari config tanpa menghapus yang sudah terbuka. */
    public function run(): void
    {
        foreach (config('gamification.achievements') as $data) {
            Achievement::updateOrCreate(
                ['code' => $data['code']],
                Arr::except($data, ['code'])
            );
        }
    }
}
