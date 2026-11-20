@extends('layouts.app')

@section('title', 'Profil - EcoScan AI')

@section('header')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-800 via-green-700 to-emerald-600 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="pointer-events-none absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="relative flex items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white/20 text-2xl font-extrabold">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h1 class="truncate text-3xl font-extrabold tracking-tight">{{ $user->name }}</h1>
                <p class="mt-1 text-sm text-green-50">
                    {{ $eco['icon'] }} Level {{ $eco['level'] }} · {{ $eco['name'] }}
                    @if ($user->is_guest) · Akun Guest @endif
                </p>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        @if (session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- RINGKASAN --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                ['Total XP', number_format($eco['xp'])],
                ['Total Scan', $totalScans],
                ['Streak Aktif', $currentStreak . ' hari'],
                ['Badge', $badges->count() . '/' . $totalAchievements],
            ] as [$label, $value])
                <div class="rounded-2xl border border-green-100 bg-white p-5 text-center shadow-sm">
                    <p class="text-2xl font-extrabold text-green-800">{{ $value }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- PROGRES LEVEL --}}
        <div class="rounded-3xl border border-green-100 bg-white p-6 shadow-sm">
            @if (! $eco['is_max'])
                <div class="mb-2 flex justify-between text-xs text-gray-500">
                    <span>Menuju {{ $eco['next_icon'] }} {{ $eco['next_name'] }}</span>
                    <span>{{ $eco['into'] }}/{{ $eco['span'] }} XP</span>
                </div>
            @else
                <div class="mb-2 text-xs font-semibold text-green-700">Level tertinggi tercapai 🎉</div>
            @endif
            <div class="h-3 overflow-hidden rounded-full bg-gray-100">
                <div class="h-full rounded-full bg-gradient-to-r from-green-500 to-emerald-400" style="width: {{ $eco['pct'] }}%"></div>
            </div>
        </div>

        {{-- BADGE --}}
        <div class="rounded-3xl border border-green-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-extrabold text-gray-900">Badge Dimiliki</h2>
                <a href="{{ route('achievements') }}" class="text-sm font-semibold text-green-700 hover:underline">Lihat semua</a>
            </div>

            @if ($badges->count())
                <div class="mt-4 flex flex-wrap gap-3">
                    @foreach ($badges as $badge)
                        <div class="flex items-center gap-2 rounded-full border border-green-200 bg-green-50 px-4 py-2" title="{{ $badge->description }}">
                            <span class="text-xl">{{ $badge->icon }}</span>
                            <span class="text-sm font-semibold text-green-800">{{ $badge->name }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-sm text-gray-500">Belum ada badge. Selesaikan scan pertamamu untuk membuka badge pertama.</p>
            @endif
        </div>

        {{-- UBAH NAMA --}}
        <form action="{{ route('profile.update') }}" method="POST" class="rounded-3xl border border-green-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <h2 class="text-xl font-extrabold text-gray-900">Ubah Nama</h2>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <input type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="60" required
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100">
                <button type="submit"
                        class="rounded-xl bg-green-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-green-800">
                    Simpan
                </button>
            </div>

            @error('name')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </form>
    </div>
@endsection
