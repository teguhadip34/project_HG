<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Contoh hadiah. Ganti sesuai hadiah yang benar-benar kamu sediakan.
     *
     * Aman dijalankan berulang (misalnya di setiap deploy): hadiah yang sudah ada
     * TIDAK ditimpa, jadi stok yang sudah berkurang dan hadiah yang sudah
     * dinonaktifkan tidak kembali ke kondisi awal. Hanya hadiah baru yang ditambahkan.
     */
    public function run(): void
    {
        $rewards = [
            ['name' => 'Stiker Digital Eco Warrior', 'icon' => '🌿', 'cost_xp' => 50,  'stock' => null, 'description' => 'Stiker digital koleksi untuk pejuang lingkungan.'],
            ['name' => 'Sertifikat Digital Eco Hero', 'icon' => '📜', 'cost_xp' => 200, 'stock' => null, 'description' => 'Sertifikat digital atas kontribusimu memilah sampah.'],
            ['name' => 'Tote Bag EcoScan',            'icon' => '👜', 'cost_xp' => 400, 'stock' => 20,   'description' => 'Tas belanja kain ramah lingkungan.'],
            ['name' => 'Tumbler Ramah Lingkungan',    'icon' => '🥤', 'cost_xp' => 800, 'stock' => 10,   'description' => 'Tumbler pakai-ulang untuk kurangi sampah plastik.'],
        ];

        foreach ($rewards as $data) {
            Reward::firstOrCreate(
                ['name' => $data['name']],
                $data + ['is_active' => true]
            );
        }
    }
}