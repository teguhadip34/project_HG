@extends('layouts.app')

@section('title', 'Dashboard - EcoScan AI')

@section('header')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-800 via-green-700 to-emerald-600 px-6 py-7 text-white shadow-lg sm:px-8 sm:py-8">

        <div class="pointer-events-none absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-24 right-32 h-56 w-56 rounded-full bg-emerald-300/10"></div>

        <div class="relative">

            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-300"></span>
                    EcoScan AI
                </span>

                <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs text-green-50">
                    AI Waste Classification
                </span>
            </div>

            <p class="text-xs font-medium uppercase tracking-wider text-green-100">
                {{ now()->format('l, d F Y') }}
            </p>

            <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                Selamat Datang!
            </h1>

            <p class="mt-2 max-w-xl text-sm text-green-50 sm:text-base">
                Mari jaga lingkungan dengan memilah sampah dan mulai dari langkah kecil hari ini.
            </p>

        </div>
    </div>
@endsection


@section('content')

    @php
        $total = $stats['total'];

        $organik = $stats['organik'];
        $anorganik = $stats['anorganik'];
        $lainnya = $stats['lainnya'];

        $organikPercent = $total > 0 ? round(($organik / $total) * 100) : 0;
        $anorganikPercent = $total > 0 ? round(($anorganik / $total) * 100) : 0;
        $lainnyaPercent = $total > 0 ? round(($lainnya / $total) * 100) : 0;
    @endphp


    <div class="space-y-6">


        {{-- ========================================= --}}
        {{-- CTA SCAN --}}
        {{-- ========================================= --}}

        <div class="relative overflow-hidden rounded-3xl border border-green-100 bg-gradient-to-r from-green-50 via-emerald-50 to-green-100 p-6 shadow-sm sm:p-7">

            <div class="pointer-events-none absolute -right-8 -top-12 text-[150px] leading-none opacity-10">
                ♻️
            </div>

            <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                <div class="max-w-2xl">

                    <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm">
                        🔍
                    </div>

                    <p class="text-xs font-bold uppercase tracking-wider text-green-600">
                        Mulai Sekarang
                    </p>

                    <h2 class="mt-1 text-2xl font-extrabold text-green-950 sm:text-3xl">
                        Kenali sampahmu dengan AI
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-green-800 sm:text-base">
                        Ambil foto atau upload gambar sampah untuk mengetahui
                        jenis dan cara pengelolaannya.
                    </p>

                </div>

                <a href="{{ route('scan') }}"
                   class="group flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-green-700 to-emerald-600 px-7 py-4 text-sm font-bold text-white shadow-lg shadow-green-200 transition hover:-translate-y-0.5 hover:from-green-800 hover:to-emerald-700">

                    <span class="text-lg transition-transform group-hover:rotate-12">
                        📷
                    </span>

                    Scan Sekarang

                    <span class="transition-transform group-hover:translate-x-1">
                        →
                    </span>

                </a>

            </div>
        </div>


        {{-- ========================================= --}}
        {{-- STATISTIK --}}
        {{-- ========================================= --}}

        <div>

            <div class="mb-4">
                <p class="text-xs font-bold uppercase tracking-wider text-green-600">
                    Aktivitas Kamu
                </p>

                <h2 class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">
                    Ringkasan Aktivitas
                </h2>
            </div>


            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">


                {{-- TOTAL --}}
                <div class="overflow-hidden rounded-2xl border border-green-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="h-1 bg-gradient-to-r from-green-500 to-emerald-400"></div>

                    <div class="p-5">

                        <div class="mb-4 flex items-center justify-between">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 text-lg">
                                🔍
                            </div>

                            <span class="text-xs font-semibold text-green-600">
                                Total
                            </span>

                        </div>

                        <p class="text-3xl font-extrabold text-green-800">
                            {{ $total }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500 sm:text-sm">
                            Total Scanning
                        </p>

                    </div>
                </div>


                {{-- ORGANIK --}}
                <div class="overflow-hidden rounded-2xl border border-lime-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="h-1 bg-gradient-to-r from-lime-500 to-green-400"></div>

                    <div class="p-5">

                        <div class="mb-4 flex items-center justify-between">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-lime-100 text-lg">
                                🌱
                            </div>

                            <span class="text-xs font-semibold text-lime-600">
                                Organik
                            </span>

                        </div>

                        <p class="text-3xl font-extrabold text-green-800">
                            {{ $organik }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500 sm:text-sm">
                            Sampah Organik
                        </p>

                    </div>
                </div>


                {{-- ANORGANIK --}}
                <div class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="h-1 bg-gradient-to-r from-emerald-500 to-teal-400"></div>

                    <div class="p-5">

                        <div class="mb-4 flex items-center justify-between">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-lg">
                                ♻️
                            </div>

                            <span class="text-xs font-semibold text-emerald-600">
                                Anorganik
                            </span>

                        </div>

                        <p class="text-3xl font-extrabold text-green-800">
                            {{ $anorganik }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500 sm:text-sm">
                            Sampah Anorganik
                        </p>

                    </div>
                </div>


                {{-- LAINNYA --}}
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="h-1 bg-gradient-to-r from-gray-400 to-gray-300"></div>

                    <div class="p-5">

                        <div class="mb-4 flex items-center justify-between">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-lg">
                                🗑️
                            </div>

                            <span class="text-xs font-semibold text-gray-500">
                                Lainnya
                            </span>

                        </div>

                        <p class="text-3xl font-extrabold text-green-800">
                            {{ $lainnya }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500 sm:text-sm">
                            Sampah Lainnya
                        </p>

                    </div>
                </div>

            </div>
        </div>


        {{-- ========================================= --}}
        {{-- DISTRIBUSI + ECO PROGRESS --}}
        {{-- ========================================= --}}

        <div class="grid gap-6 lg:grid-cols-3">


            {{-- DISTRIBUSI --}}
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">

                <div class="mb-6 flex items-center justify-between">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-green-600">
                            Statistik
                        </p>

                        <h2 class="mt-1 text-xl font-bold text-gray-900">
                            Distribusi Sampah
                        </h2>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50">
                        📊
                    </div>

                </div>


                {{-- ORGANIK --}}
                <div class="mb-5">

                    <div class="mb-2 flex justify-between text-sm">

                        <span class="font-semibold text-gray-700">
                            🌱 Organik
                        </span>

                        <span class="font-bold text-green-700">
                            {{ $organik }} ({{ $organikPercent }}%)
                        </span>

                    </div>

                    <div class="h-3 overflow-hidden rounded-full bg-gray-100">

                        <div
                            class="h-full rounded-full bg-gradient-to-r from-lime-400 to-green-500 transition-all duration-700"
                            style="width: {{ $organikPercent }}%">
                        </div>

                    </div>

                </div>


                {{-- ANORGANIK --}}
                <div class="mb-5">

                    <div class="mb-2 flex justify-between text-sm">

                        <span class="font-semibold text-gray-700">
                            ♻️ Anorganik
                        </span>

                        <span class="font-bold text-green-700">
                            {{ $anorganik }} ({{ $anorganikPercent }}%)
                        </span>

                    </div>

                    <div class="h-3 overflow-hidden rounded-full bg-gray-100">

                        <div
                            class="h-full rounded-full bg-gradient-to-r from-green-500 to-emerald-500 transition-all duration-700"
                            style="width: {{ $anorganikPercent }}%">
                        </div>

                    </div>

                </div>


                {{-- LAINNYA --}}
                <div>

                    <div class="mb-2 flex justify-between text-sm">

                        <span class="font-semibold text-gray-700">
                            🗑️ Lainnya
                        </span>

                        <span class="font-bold text-green-700">
                            {{ $lainnya }} ({{ $lainnyaPercent }}%)
                        </span>

                    </div>

                    <div class="h-3 overflow-hidden rounded-full bg-gray-100">

                        <div
                            class="h-full rounded-full bg-gradient-to-r from-gray-400 to-gray-500 transition-all duration-700"
                            style="width: {{ $lainnyaPercent }}%">
                        </div>

                    </div>

                </div>

            </div>


            {{-- ECO PROGRESS --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-800 to-emerald-700 p-6 text-white shadow-lg">

                <div class="pointer-events-none absolute -right-8 -top-8 text-8xl opacity-10">
                    {{ $eco['icon'] }}
                </div>

                <div class="relative">

                    <div class="mb-5 flex items-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 text-xl">
                            {{ $eco['icon'] }}
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-green-200">
                                Eco Progress · Level {{ $eco['level'] }}
                            </p>

                            <h2 class="font-bold">
                                {{ $eco['name'] }}
                            </h2>
                        </div>

                    </div>


                    <p class="text-sm text-green-100">
                        Total pengalaman kamu
                    </p>

                    <p class="mt-1 text-4xl font-extrabold">
                        {{ number_format($eco['xp']) }} XP
                    </p>


                    <div class="mt-5">

                        <div class="mb-2 flex justify-between text-xs text-green-100">
                            @if (! $eco['is_max'])
                                <span>Menuju {{ $eco['next_icon'] }} {{ $eco['next_name'] }}</span>
                                <span>{{ $eco['into'] }}/{{ $eco['span'] }} XP</span>
                            @else
                                <span>Level tertinggi</span>
                                <span>MAX</span>
                            @endif
                        </div>

                        <div class="h-3 overflow-hidden rounded-full bg-white/20">

                            <div
                                class="h-full rounded-full bg-emerald-300 transition-all duration-700"
                                style="width: {{ $eco['pct'] }}%">
                            </div>

                        </div>

                    </div>


                    @if (! $eco['is_max'])

                        <p class="mt-4 text-xs leading-relaxed text-green-100">
                            Tinggal
                            <strong class="text-white">
                                {{ $eco['xp_to_next'] }} XP
                            </strong>
                            lagi untuk naik ke level {{ $eco['level'] + 1 }}!
                        </p>

                    @else

                        <p class="mt-4 text-xs leading-relaxed text-green-100">
                            🎉 Hebat! Kamu sudah mencapai level tertinggi.
                            Terus jaga kebiasaan memilah sampah!
                        </p>

                    @endif


                    <a href="{{ route('achievements') }}"
                       class="mt-5 flex w-full items-center justify-between rounded-xl bg-white/15 px-4 py-3 text-sm font-bold transition hover:bg-white/25">
                        <span>🏅 Lihat Pencapaian</span>
                        <span>{{ $achievementsUnlocked }}/{{ $achievementsTotal }}</span>
                    </a>

                </div>

            </div>

        </div>


        {{-- ========================================= --}}
        {{-- RIWAYAT + AI INSIGHT --}}
        {{-- ========================================= --}}

        <div class="grid gap-6 lg:grid-cols-3">


            {{-- RIWAYAT --}}
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">

                <div class="mb-5 flex items-center justify-between">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-green-600">
                            Riwayat
                        </p>

                        <h2 class="mt-1 text-xl font-bold text-gray-900">
                            Aktivitas Terakhir
                        </h2>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50">
                        🕒
                    </div>

                </div>


                <div class="space-y-3">

                    @forelse ($recent as $item)

                        <div class="group flex items-center gap-4 rounded-2xl border border-gray-100 bg-gray-50 p-4 transition hover:border-green-100 hover:bg-green-50">

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-xl shadow-sm">
                                ♻️
                            </div>


                            <div class="min-w-0 flex-1">

                                <p class="truncate font-bold text-gray-900">
                                    {{ $item['nama'] }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    Hasil klasifikasi EcoScan AI
                                </p>

                            </div>


                            <div class="text-right">

                                <span class="rounded-full bg-green-100 px-3 py-1.5 text-xs font-bold text-green-700">
                                    {{ $item['kategori'] }}
                                </span>

                                <p class="mt-2 text-xs text-gray-400">
                                    Terdeteksi
                                </p>

                            </div>

                        </div>

                    @empty

                        <div class="rounded-2xl border border-dashed border-green-200 bg-green-50 p-8 text-center">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm">
                                🔍
                            </div>

                            <p class="mt-4 font-bold text-green-900">
                                Belum ada aktivitas
                            </p>

                            <p class="mt-1 text-xs text-green-700">
                                Coba scan sampah pertamamu untuk melihat hasilnya di sini.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>


            {{-- AI INSIGHT --}}
            <div class="relative overflow-hidden rounded-3xl border border-green-100 bg-gradient-to-br from-green-50 to-emerald-50 p-6">

                <div class="pointer-events-none absolute -right-5 -top-5 text-7xl opacity-10">
                    🤖
                </div>

                <div class="relative">

                    <div class="mb-5 flex items-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-600 text-xl text-white shadow-sm">
                            🤖
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-green-600">
                                AI Insight
                            </p>

                            <h2 class="font-bold text-green-950">
                                Analisis Aktivitas
                            </h2>
                        </div>

                    </div>


                    @if ($total > 0)

                        @if ($anorganik >= $organik && $anorganik >= $lainnya)

                            <div class="rounded-2xl bg-white p-4 shadow-sm">

                                <p class="text-sm leading-relaxed text-green-900">
                                    Sampah yang paling banyak kamu scan saat ini adalah
                                    <strong>Anorganik</strong>.
                                </p>

                                <p class="mt-2 text-xs leading-relaxed text-gray-500">
                                    Coba perhatikan kembali cara pemilahan sampah anorganik
                                    agar dapat didaur ulang dengan lebih baik.
                                </p>

                            </div>

                        @elseif ($organik >= $anorganik && $organik >= $lainnya)

                            <div class="rounded-2xl bg-white p-4 shadow-sm">

                                <p class="text-sm leading-relaxed text-green-900">
                                    Sampah yang paling banyak kamu scan saat ini adalah
                                    <strong>Organik</strong>.
                                </p>

                                <p class="mt-2 text-xs leading-relaxed text-gray-500">
                                    Sampah organik dapat dikelola menjadi kompos
                                    jika dipisahkan dengan benar.
                                </p>

                            </div>

                        @else

                            <div class="rounded-2xl bg-white p-4 shadow-sm">

                                <p class="text-sm leading-relaxed text-green-900">
                                    Kamu memiliki beberapa jenis sampah
                                    yang masuk kategori <strong>Lainnya</strong>.
                                </p>

                                <p class="mt-2 text-xs leading-relaxed text-gray-500">
                                    Pastikan sampah tersebut diperiksa kembali
                                    sebelum menentukan cara pembuangannya.
                                </p>

                            </div>

                        @endif

                    @else

                        <div class="rounded-2xl bg-white p-5 text-center shadow-sm">

                            <div class="text-3xl">
                                🤖
                            </div>

                            <p class="mt-3 text-sm font-semibold text-green-900">
                                Belum ada data AI
                            </p>

                            <p class="mt-1 text-xs leading-relaxed text-gray-500">
                                Lakukan scan pertama untuk mendapatkan insight dari EcoScan AI.
                            </p>

                        </div>

                    @endif


                    <a href="{{ route('scan') }}"
                       class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-green-700 py-3 text-sm font-bold text-white transition hover:bg-green-800">
                        Scan Sekarang
                        <span>→</span>
                    </a>

                </div>

            </div>

        </div>


        {{-- ========================================= --}}
        {{-- ECO TIPS --}}
        {{-- ========================================= --}}

        <div class="rounded-3xl border border-green-100 bg-gradient-to-r from-green-50 to-emerald-50 p-6 sm:p-7">

            <div class="mb-6 flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-600 text-xl text-white">
                    💡
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-green-600">
                        Eco Tips
                    </p>

                    <h2 class="text-xl font-bold text-green-950">
                        Tips Hari Ini
                    </h2>
                </div>

            </div>


            <div class="grid gap-4 md:grid-cols-3">

                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="mb-3 text-2xl">
                        ♻️
                    </div>

                    <h3 class="font-bold text-green-900">
                        Pilah dari sumbernya
                    </h3>

                    <p class="mt-2 text-xs leading-relaxed text-gray-500">
                        Pisahkan sampah sesuai jenisnya sebelum membuangnya
                        agar lebih mudah dikelola.
                    </p>

                </div>


                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="mb-3 text-2xl">
                        🌱
                    </div>

                    <h3 class="font-bold text-green-900">
                        Kurangi penggunaan
                    </h3>

                    <p class="mt-2 text-xs leading-relaxed text-gray-500">
                        Gunakan barang yang dapat dipakai kembali
                        untuk mengurangi jumlah sampah.
                    </p>

                </div>


                <div class="rounded-2xl bg-white p-5 shadow-sm">

                    <div class="mb-3 text-2xl">
                        🌍
                    </div>

                    <h3 class="font-bold text-green-900">
                        Jaga lingkungan
                    </h3>

                    <p class="mt-2 text-xs leading-relaxed text-gray-500">
                        Kebiasaan memilah sampah dari sekarang
                        dapat memberikan dampak positif bagi lingkungan.
                    </p>

                </div>

            </div>

</div>


<!-- Penjelasan Jenis Sampah -->
<section class="bg-emerald-50 border border-emerald-100 rounded-3xl p-6 mt-6">

    <!-- Header -->
    <div class="mb-5">
        <span class="text-xs font-semibold text-emerald-600 uppercase">
            Edukasi Sampah
        </span>
        <h2 class="text-xl font-bold text-gray-900 mt-1">
            Mengenal Jenis Sampah
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Kenali jenis sampah agar lebih mudah memilahnya.
        </p>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- Organik -->
        <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="w-11 h-11 bg-green-100 rounded-xl flex items-center justify-center text-2xl mb-4">
                🌱
            </div>
            <h3 class="font-bold text-emerald-800 text-base mb-2">
                Sampah Organik
            </h3>
            <p class="text-sm text-gray-500 leading-relaxed">
                Sampah yang mudah terurai secara alami, seperti sisa makanan,
                daun kering, kulit buah, dan sayuran.
            </p>
        </div>

        <!-- Anorganik -->
        <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center text-2xl mb-4">
                ♻️
            </div>
            <h3 class="font-bold text-emerald-800 text-base mb-2">
                Sampah Anorganik
            </h3>
            <p class="text-sm text-gray-500 leading-relaxed">
                Sampah yang dapat didaur ulang atau digunakan kembali,
                seperti plastik, botol kaca, kertas, dan kaleng.
            </p>
        </div>

        <!-- Residu -->
        <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="w-11 h-11 bg-orange-100 rounded-xl flex items-center justify-center text-2xl mb-4">
                🗑️
            </div>
            <h3 class="font-bold text-emerald-800 text-base mb-2">
                Sampah Residu
            </h3>
            <p class="text-sm text-gray-500 leading-relaxed">
                Sampah yang sulit atau tidak dapat didaur ulang,
                seperti popok sekali pakai, tisu kotor, dan puntung rokok.
            </p>
        </div>

    </div>
</section>

        {{-- FOOTER --}}
        <div class="pb-4 text-center">

            <p class="text-xs text-gray-400">
                🌍 Pilah sampah hari ini untuk lingkungan yang lebih baik esok hari.
            </p>

        </div>

    </div>


@if (Auth::check())
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
            Logout
        </button>
    </form>
@endif

@endsection
