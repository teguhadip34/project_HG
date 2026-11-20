<?php

namespace Database\Seeders;

use App\Models\WasteCategory;
use App\Models\WasteType;
use Illuminate\Database\Seeder;

class WasteSeeder extends Seeder
{
    public function run(): void
    {
        $organik = WasteCategory::firstOrCreate([
            'name' => 'Organik',
        ]);

        $anorganik = WasteCategory::firstOrCreate([
            'name' => 'Anorganik',
        ]);

        $lainnya = WasteCategory::firstOrCreate([
            'name' => 'Lainnya',
        ]);

        $types = [
            [
                'label' => 'plastic',
                'name' => 'Plastik',
                'category' => $anorganik,
                'tip' => 'Bersihkan, lalu setor ke bank sampah atau tempat daur ulang.',
            ],
            [
                'label' => 'metal',
                'name' => 'Logam',
                'category' => $anorganik,
                'tip' => 'Bersihkan dan setor ke tempat daur ulang.',
            ],
            [
                'label' => 'glass',
                'name' => 'Kaca',
                'category' => $anorganik,
                'tip' => 'Pisahkan dan setor ke tempat daur ulang.',
            ],
            [
                'label' => 'paper',
                'name' => 'Kertas',
                'category' => $anorganik,
                'tip' => 'Pastikan kering, lalu kumpulkan untuk didaur ulang.',
            ],
            [
                'label' => 'cardboard',
                'name' => 'Kardus',
                'category' => $anorganik,
                'tip' => 'Lipat dan kumpulkan untuk didaur ulang.',
            ],
            [
                'label' => 'e-waste',
                'name' => 'Sampah Elektronik',
                'category' => $lainnya,
                'tip' => 'Serahkan ke tempat pengumpulan e-waste.',
            ],
            [
                'label' => 'medical',
                'name' => 'Sampah Medis',
                'category' => $lainnya,
                'tip' => 'Serahkan ke fasilitas kesehatan.',
            ],
        ];

        foreach ($types as $type) {
            WasteType::updateOrCreate(
                ['label' => $type['label']],
                [
                    'name' => $type['name'],
                    'waste_category_id' => $type['category']->id,
                    'disposal_tip' => $type['tip'],
                ]
            );
        }
    }
}