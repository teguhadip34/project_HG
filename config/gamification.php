<?php

/*
 * Pengaturan gamifikasi EcoScan.
 * Semua angka XP, level, dan achievement diatur di sini supaya mudah diubah
 * tanpa menyentuh logika. Setelah mengubah 'achievements', jalankan:
 *   php artisan db:seed --class=AchievementSeeder
 */

return [

    // XP yang didapat dari satu scan yang berhasil dikenali.
    'xp' => [
        'scan'                      => 10, // scan berhasil dikenali
        'high_confidence'           => 5,  // bonus kalau keyakinan AI tinggi
        'high_confidence_threshold' => 80, // batas "tinggi" (persen)
        'new_type'                  => 5,  // bonus pertama kali menemukan jenis sampah itu
    ],

    // Level ditentukan oleh total XP.
    'levels' => [
        1 => ['name' => 'Eco Beginner', 'icon' => '🌱', 'xp' => 0],
        2 => ['name' => 'Eco Explorer', 'icon' => '🌿', 'xp' => 150],
        3 => ['name' => 'Eco Recycler', 'icon' => '♻️', 'xp' => 400],
        4 => ['name' => 'Eco Guardian', 'icon' => '🌳', 'xp' => 800],
        5 => ['name' => 'Eco Hero',     'icon' => '🌍', 'xp' => 1500],
        6 => ['name' => 'Eco Legend',   'icon' => '🏆', 'xp' => 3000],
    ],

    /*
     * Achievement. 'metric' yang tersedia (lihat GamificationService::metrics):
     *   scans            -> jumlah scan yang menghasilkan XP
     *   distinct_types   -> jumlah jenis sampah berbeda yang pernah di-scan
     *   anorganik_scans  -> jumlah scan sampah anorganik
     *   streak_days      -> rekor hari berturut-turut melakukan scan
     */
    'achievements' => [
        ['code' => 'first_step',       'name' => 'First Step',       'icon' => '🌱', 'description' => 'Selesaikan scan pertama.',                 'metric' => 'scans',           'target' => 1,   'xp_reward' => 10],
        ['code' => 'getting_started',  'name' => 'Getting Started',  'icon' => '🚀', 'description' => 'Selesaikan 10 scan.',                     'metric' => 'scans',           'target' => 10,  'xp_reward' => 50],
        ['code' => 'waste_hunter',     'name' => 'Waste Hunter',     'icon' => '🔎', 'description' => 'Scan 5 jenis sampah yang berbeda.',        'metric' => 'distinct_types',  'target' => 5,   'xp_reward' => 40],
        ['code' => 'waste_collector',  'name' => 'Waste Collector',  'icon' => '🧺', 'description' => 'Temukan ke-7 jenis sampah yang dikenali AI.', 'metric' => 'distinct_types',  'target' => 7,   'xp_reward' => 100],
        ['code' => 'recycling_master', 'name' => 'Recycling Master', 'icon' => '♻️', 'description' => 'Scan 20 sampah anorganik.',                'metric' => 'anorganik_scans', 'target' => 20,  'xp_reward' => 100],
        ['code' => 'eco_streak',       'name' => 'Eco Streak',       'icon' => '🔥', 'description' => 'Scan 3 hari berturut-turut.',              'metric' => 'streak_days',     'target' => 3,   'xp_reward' => 30],
        ['code' => 'eco_champion',     'name' => 'Eco Champion',     'icon' => '🏆', 'description' => 'Capai 100 scan.',                          'metric' => 'scans',           'target' => 100, 'xp_reward' => 300],
    ],

    /*
     * Tantangan harian & mingguan. Tidak perlu seeder: ubah daftar ini lalu selesai.
     *   period -> daily (reset tiap hari) | weekly (reset tiap Senin)
     *   metric -> scans (jumlah scan) | distinct_types (jumlah jenis sampah berbeda)
     * Hanya scan yang menghasilkan XP yang dihitung (scan duplikat tidak).
     */
    'challenges' => [
        ['code' => 'daily_scan_3',    'period' => 'daily',  'icon' => '📸', 'name' => 'Scan 3 sampah hari ini',                  'metric' => 'scans',          'target' => 3,  'xp_reward' => 15],
        ['code' => 'daily_types_2',   'period' => 'daily',  'icon' => '🔎', 'name' => 'Scan 2 jenis sampah berbeda hari ini',    'metric' => 'distinct_types', 'target' => 2,  'xp_reward' => 10],
        ['code' => 'weekly_types_5',  'period' => 'weekly', 'icon' => '🧺', 'name' => 'Scan 5 jenis sampah berbeda minggu ini',  'metric' => 'distinct_types', 'target' => 5,  'xp_reward' => 40],
        ['code' => 'weekly_scan_20',  'period' => 'weekly', 'icon' => '🏅', 'name' => 'Selesaikan 20 scan minggu ini',            'metric' => 'scans',          'target' => 20, 'xp_reward' => 75],
    ],
];
