@extends('layouts.app')

@section('title', 'Hasil Scan - EcoScan AI')

@section('header')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-green-800 via-green-700 to-emerald-600 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="pointer-events-none absolute -right-10 -top-16 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-20 right-24 h-48 w-48 rounded-full bg-white/5"></div>
        <div class="relative">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur">
                <span class="h-2 w-2 animate-pulse rounded-full bg-green-300"></span> EcoScan AI
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Hasil Analisis Sampah</h1>
            <p class="mt-2 max-w-xl text-sm text-green-50 sm:text-base">AI telah selesai menganalisis gambar sampah kamu. Berikut hasil klasifikasinya.</p>
        </div>
    </div>
@endsection

@section('content')
    @php
        $reward = session('gamification');
        $categoryNames = ['Organik', 'Anorganik', 'Residu', 'B3'];
        $storedCategories = collect(data_get($scan->detection_summary, 'categories', []));
        $legacyCategory = data_get($scan->detection_summary, 'category');

        if ($legacyCategory && ! $storedCategories->contains('name', $legacyCategory['name'])) {
            $storedCategories->push($legacyCategory);
        }

        $topCategories = collect($categoryNames)
            ->map(fn ($name) => $storedCategories->firstWhere('name', $name) ?? ['name' => $name, 'confidence' => 0])
            ->sortByDesc('confidence')
            ->values();
        $topCategory = $topCategories[0];
    @endphp

    @if ($reward && ($reward['xp_gained'] > 0 || $reward['achievement_xp'] > 0))
        <style>
            @keyframes eco-pop { 0% { opacity: 0; transform: scale(.85) translateY(12px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
            .eco-pop { animation: eco-pop .35s ease-out both; }
        </style>
        <div id="rewardModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="eco-pop max-h-[90vh] w-full max-w-sm overflow-y-auto rounded-3xl bg-white p-6 text-center shadow-2xl">
                @if ($reward['leveled_up'])
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">🎉 Level Up!</p>
                    <div class="mt-3 animate-bounce text-6xl">{{ $reward['progress']['icon'] }}</div>
                    <h2 class="mt-3 text-2xl font-extrabold text-green-900">{{ $reward['progress']['name'] }}</h2>
                    <p class="mt-1 text-sm text-gray-500">Kamu mencapai level {{ $reward['progress']['level'] }}!</p>
                @else
                    <div class="text-5xl">✨</div>
                    <h2 class="mt-3 text-2xl font-extrabold text-green-900">+{{ $reward['xp_gained'] + $reward['achievement_xp'] + ($reward['challenge_xp'] ?? 0) }} XP</h2>
                @endif
                <div class="mt-5 space-y-2 rounded-2xl bg-green-50 p-4 text-left text-sm">
                    @foreach ($reward['breakdown'] as $line)
                        <div class="flex justify-between"><span class="text-green-900">{{ $line['label'] }}</span><span class="font-bold text-green-700">+{{ $line['xp'] }}</span></div>
                    @endforeach
                </div>
                @foreach ($reward['achievements'] as $badge)
                    <div class="mt-3 flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-3 text-left">
                        <div class="text-3xl">{{ $badge['icon'] }}</div>
                        <div class="min-w-0 flex-1"><p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Badge baru</p><p class="truncate font-bold text-gray-900">{{ $badge['name'] }}</p><p class="text-xs text-gray-500">{{ $badge['description'] }}</p></div>
                        <span class="text-sm font-bold text-amber-600">+{{ $badge['xp_reward'] }}</span>
                    </div>
                @endforeach
                @foreach (($reward['challenges'] ?? []) as $challenge)
                    <div class="mt-3 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-left">
                        <div class="text-3xl">{{ $challenge['icon'] }}</div>
                        <div class="min-w-0 flex-1"><p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Tantangan selesai</p><p class="truncate font-bold text-gray-900">{{ $challenge['name'] }}</p></div>
                        <span class="text-sm font-bold text-emerald-600">+{{ $challenge['xp_reward'] }}</span>
                    </div>
                @endforeach
                <div class="mt-5">
                    @if (! $reward['progress']['is_max'])
                        <div class="mb-1 flex justify-between text-xs text-gray-500"><span>Menuju {{ $reward['progress']['next_name'] }}</span><span>{{ $reward['progress']['into'] }}/{{ $reward['progress']['span'] }} XP</span></div>
                    @endif
                    <div class="h-2.5 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full bg-gradient-to-r from-green-500 to-emerald-400" style="width: {{ $reward['progress']['pct'] }}%"></div></div>
                </div>
                <button type="button" id="rewardClose" class="mt-6 w-full rounded-2xl bg-gradient-to-r from-green-700 to-emerald-600 py-3 font-bold text-white shadow-lg shadow-green-100 transition hover:from-green-800 hover:to-emerald-700">Lanjut</button>
            </div>
        </div>
        <script>
            (() => {
                const modal = document.getElementById('rewardModal');
                const close = () => modal.remove();
                document.getElementById('rewardClose').addEventListener('click', close);
                modal.addEventListener('click', (event) => { if (event.target === modal) close(); });
            })();
        </script>
    @endif

    <div class="mx-auto max-w-5xl space-y-6">
        @if ($reward && $reward['duplicate'])
            <div class="flex items-start gap-3 rounded-2xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800"><span class="text-lg">⚠️</span><p>Foto ini sudah pernah di-scan sebelumnya, jadi tidak ada XP tambahan. Coba scan sampah yang lain!</p></div>
        @endif

        <div class="relative overflow-hidden rounded-3xl border {{ $scan->wasteType ? 'border-green-100' : 'border-yellow-100' }} bg-white shadow-sm">
            <div class="h-1.5 bg-gradient-to-r {{ $scan->wasteType ? 'from-green-600 via-emerald-500 to-green-400' : 'from-yellow-400 to-orange-400' }}"></div>
            <div class="grid gap-7 p-5 sm:p-7 md:grid-cols-2">
                <div>
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <p class="text-sm font-bold text-gray-800">Foto yang dianalisis</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full {{ $scan->wasteType ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }} px-3 py-1 text-xs font-semibold">
                            <span class="h-2 w-2 rounded-full {{ $scan->wasteType ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                            {{ $scan->wasteType ? 'Berhasil dianalisis' : 'Perlu gambar lebih jelas' }}
                        </span>
                    </div>
                    <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-gray-50 p-3">
                        @if ($scan->image_data)
                            <img src="{{ $scan->annotated_image_data ?: $scan->image_data }}" class="mx-auto h-72 w-full rounded-xl object-contain transition duration-300 group-hover:scale-[1.02] sm:h-80" alt="Gambar sampah yang dianalisis">
                        @else
                            <div class="flex h-72 items-center justify-center text-sm text-gray-400 sm:h-80">Foto tidak tersedia</div>
                        @endif
                        <div class="pointer-events-none absolute inset-x-3 bottom-3 rounded-xl bg-gradient-to-t from-black/40 to-transparent p-4 pt-12"><p class="text-xs font-medium text-white">Gambar hasil pemindaian</p></div>
                    </div>
                </div>

                <div class="flex flex-col justify-center">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wider text-green-600">Hasil Deteksi AI</p>
                    <div class="mt-1 rounded-2xl bg-gradient-to-r from-green-700 to-emerald-600 p-5 text-white shadow-lg shadow-green-100">
                        <p class="text-xs font-bold uppercase tracking-wider text-green-100">Prediksi utama · Top 1</p>
                        <div class="mt-2 flex items-end justify-between gap-3"><h2 class="text-3xl font-extrabold sm:text-4xl">{{ $topCategory['name'] }}</h2><p class="shrink-0 text-3xl font-extrabold">{{ number_format((float) $topCategory['confidence'], 2) }}%</p></div>
                        <p class="mt-2 text-xs text-green-100">Label AI: {{ $scan->detected_label ?: $scan->wasteType?->name ?: 'Belum dikenali' }}</p>
                    </div>
                    <div class="mt-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">3 jenis sampah lainnya</p>
                        <div class="mt-3 space-y-2">
                            @foreach (array_slice($topCategories->all(), 1) as $index => $category)
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-white px-3 py-2.5">
                                    <div class="flex min-w-0 items-center gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-green-100 text-xs font-extrabold text-green-700">{{ $index + 2 }}</span><p class="truncate text-sm font-bold text-gray-800">{{ $category['name'] }}</p></div>
                                    <p class="shrink-0 text-sm font-bold text-green-700">{{ number_format((float) $category['confidence'], 2) }}%</p>
                                </div>
                            @endforeach
                        </div>
                        @if (! $scan->wasteType)
                            <p class="mt-3 text-[11px] text-gray-500">AI belum dapat memastikan kategori. Coba foto lebih terang, fokus, dan menampilkan satu objek dengan jelas.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($scan->wasteType?->disposal_tip)
            <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-r from-emerald-50 to-green-50 shadow-sm">
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:p-6">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm">💡</div>
                    <div class="flex-1"><p class="text-xs font-bold uppercase tracking-wider text-green-600">Tips Pengelolaan</p><h3 class="mt-1 text-lg font-bold text-green-950">Cara membuang sampah ini</h3><p class="mt-1 text-sm leading-relaxed text-green-800">{{ $scan->wasteType->disposal_tip }}</p></div>
                </div>
            </div>
        @endif

        <div>
            <div class="mb-4"><p class="text-xs font-bold uppercase tracking-wider text-green-600">Eco Tips</p><h3 class="mt-1 text-xl font-bold text-gray-900">Kenapa memilah sampah itu penting?</h3></div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-green-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md"><div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-xl">♻️</div><h4 class="font-bold text-gray-900">Mudah Didaur Ulang</h4><p class="mt-2 text-xs leading-relaxed text-gray-500">Sampah yang dipilah lebih mudah diproses dan dimanfaatkan kembali.</p></div>
                <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md"><div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-xl">🌱</div><h4 class="font-bold text-gray-900">Jaga Lingkungan</h4><p class="mt-2 text-xs leading-relaxed text-gray-500">Pemilahan membantu mengurangi jumlah sampah yang berakhir di tempat pembuangan.</p></div>
                <div class="rounded-2xl border border-lime-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md"><div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-lime-100 text-xl">🏠</div><h4 class="font-bold text-gray-900">Mulai dari Rumah</h4><p class="mt-2 text-xs leading-relaxed text-gray-500">Kebiasaan memilah sampah bisa dimulai dari hal sederhana di rumah.</p></div>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <a href="{{ route('scan') }}" class="group flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-green-700 to-emerald-600 py-4 font-bold text-white shadow-lg shadow-green-100 transition hover:-translate-y-0.5 hover:from-green-800 hover:to-emerald-700"><span class="transition-transform group-hover:-rotate-12">🔄</span> Scan Sampah Lagi</a>
            <a href="{{ route('dashboard') }}" class="group flex items-center justify-center gap-2 rounded-2xl border border-green-200 bg-white py-4 font-bold text-green-800 shadow-sm transition hover:-translate-y-0.5 hover:bg-green-50"><span>🏠</span> Kembali ke Dashboard</a>
        </div>
        <div class="pb-2 text-center"><p class="text-xs text-gray-400">🌍 Setiap sampah yang dipilah adalah langkah kecil untuk bumi yang lebih baik.</p></div>
    </div>
@endsection
