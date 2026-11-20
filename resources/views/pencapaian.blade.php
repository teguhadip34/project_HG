
@extends('layouts.app')

@section('title', 'Pencapaian - EcoScan AI')

@section('header')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-800 via-green-700 to-emerald-600 px-6 py-7 text-white shadow-lg sm:px-8">

        <div class="pointer-events-none absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/10"></div>

        <div class="relative">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur">
                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-300"></span>
                Eco Progress
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                Pencapaian
            </h1>

            <p class="mt-2 max-w-xl text-sm text-green-50 sm:text-base">
                Kumpulkan XP dari setiap scan dan buka semua badge.
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        {{-- LEVEL SAAT INI --}}
        <div class="rounded-3xl border border-green-100 bg-white p-6 shadow-sm">

            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-green-100 text-4xl">
                    {{ $eco['icon'] }}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-green-600">
                        Level {{ $eco['level'] }}
                    </p>
                    <h2 class="text-2xl font-extrabold text-gray-900">{{ $eco['name'] }}</h2>
                    <p class="text-sm text-gray-500">{{ number_format($eco['xp']) }} XP total</p>
                </div>

                <div class="text-right">
                    <p class="text-2xl font-extrabold text-green-800">{{ $unlocked }}/{{ $achievements->count() }}</p>
                    <p class="text-xs text-gray-500">badge terbuka</p>
                </div>
            </div>

            <div class="mt-5">
                @if (! $eco['is_max'])
                    <div class="mb-2 flex justify-between text-xs text-gray-500">
                        <span>Menuju {{ $eco['next_icon'] }} {{ $eco['next_name'] }}</span>
                        <span>{{ $eco['into'] }}/{{ $eco['span'] }} XP</span>
                    </div>
                @else
                    <div class="mb-2 text-xs font-semibold text-green-700">Level tertinggi tercapai 🎉</div>
                @endif

                <div class="h-3 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full rounded-full bg-gradient-to-r from-green-500 to-emerald-400 transition-all duration-700"
                         style="width: {{ $eco['pct'] }}%"></div>
                </div>
            </div>

        </div>

        {{-- JALUR LEVEL --}}
        @php
            $levels = [
                ['level' => 1, 'icon' => '🌱', 'name' => 'Eco Beginner', 'range' => '0–10 scan', 'reward' => 'First Step'],
                ['level' => 2, 'icon' => '🌿', 'name' => 'Eco Explorer', 'range' => '11–25 scan', 'reward' => 'Eco Explorer'],
                ['level' => 3, 'icon' => '♻️', 'name' => 'Eco Recycler', 'range' => '26–50 scan', 'reward' => 'Recycle Master'],
                ['level' => 4, 'icon' => '🌳', 'name' => 'Eco Guardian', 'range' => '51–100 scan', 'reward' => 'Eco Guardian'],
                ['level' => 5, 'icon' => '🌍', 'name' => 'Eco Hero', 'range' => '101–200 scan', 'reward' => 'Eco Hero'],
                ['level' => 6, 'icon' => '🏆', 'name' => 'Eco Legend', 'range' => '201+ scan', 'reward' => 'Eco Legend'],
            ];
            $currentLevel = (int) $eco['level'];
        @endphp

        <div class="rounded-3xl border border-green-100 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-extrabold text-gray-900">Jalur Level</h2>
            <p class="mb-6 mt-1 text-sm text-gray-500">
                Makin rajin memilah sampah, makin tinggi levelmu. Sampai level mana kamu bisa?
            </p>

            <ol>
                @foreach ($levels as $lv)
                    @php
                        $state = $lv['level'] < $currentLevel
                            ? 'done'
                            : ($lv['level'] === $currentLevel ? 'current' : 'locked');
                    @endphp

                    <li class="relative flex gap-4 pb-5 last:pb-0" @if ($state === 'current') aria-current="step" @endif>

                        @unless ($loop->last)
                            <span class="absolute -ml-px left-6 top-12 bottom-0 w-0.5 {{ $state === 'done' ? 'bg-emerald-400' : 'bg-gray-200' }}"
                                  aria-hidden="true"></span>
                        @endunless

                        <div class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-2xl
                                    {{ $state === 'current' ? 'bg-green-600 ring-4 ring-green-100' : ($state === 'done' ? 'bg-green-100' : 'bg-gray-100 opacity-60 grayscale') }}"
                             aria-hidden="true">
                            {{ $lv['icon'] }}
                        </div>

                        <div class="min-w-0 flex-1 rounded-2xl {{ $state === 'current' ? 'border border-green-200 bg-green-50 px-4 py-3' : 'py-1' }}">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <h3 class="font-bold {{ $state === 'locked' ? 'text-gray-500' : 'text-gray-900' }}">
                                    Level {{ $lv['level'] }} · {{ $lv['name'] }}
                                </h3>

                                @if ($state === 'current')
                                    <span class="rounded-full bg-green-600 px-2.5 py-0.5 text-xs font-bold text-white">Level kamu</span>
                                @elseif ($state === 'done')
                                    <span class="text-xs font-semibold text-green-600">✓ Selesai</span>
                                @else
                                    <span class="text-xs text-gray-400">🔒 Terkunci</span>
                                @endif
                            </div>

                            <p class="mt-1 text-sm {{ $state === 'locked' ? 'text-gray-400' : 'text-gray-500' }}">
                                {{ $lv['range'] }} · Hadiah: badge {{ $lv['reward'] }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>



        {{-- KOLEKSI BADGE --}}
        @php
            $rarityMap = [
                'common'    => ['Umum',       'bg-gray-100 text-gray-600',     'border-gray-200'],
                'rare'      => ['Langka',     'bg-blue-100 text-blue-700',     'border-blue-300'],
                'epic'      => ['Epik',       'bg-purple-100 text-purple-700', 'border-purple-300'],
                'legendary' => ['Legendaris', 'bg-amber-100 text-amber-700',   'border-amber-400'],
            ];
            $byRarity = $achievements->groupBy(fn ($row) => $row['achievement']->rarity ?? 'common');
        @endphp

        <div class="rounded-3xl border border-green-100 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-extrabold text-gray-900">Koleksi Badge</h2>
            <p class="mt-1 text-sm text-gray-500">Makin langka badge-nya, makin sulit didapat.</p>

            <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ($rarityMap as $key => [$label, $chip])
                    @php
                        $group = $byRarity->get($key, collect());
                        $got   = $group->filter(fn ($row) => $row['unlocked_at'])->count();
                    @endphp
                    <div class="rounded-2xl border border-gray-100 p-4 text-center">
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $chip }}">{{ $label }}</span>
                        <p class="mt-3 text-2xl font-extrabold text-gray-900">{{ $got }}/{{ $group->count() }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- DAFTAR BADGE / QUEST --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

            @foreach ($achievements as $row)
                @php
                    $a = $row['achievement'];
                    $done = $row['unlocked_at'] !== null;
                @endphp

                <div class="relative overflow-hidden rounded-2xl border p-5 shadow-sm transition
                            {{ $done ? $rarityMap[$a->rarity ?? 'common'][2] . ' bg-white hover:-translate-y-1 hover:shadow-md' : 'border-gray-100 bg-gray-50' }}">

                    <div class="flex items-start gap-4">

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-3xl
                                    {{ $done ? 'bg-green-100' : 'bg-gray-200 opacity-50 grayscale' }}">
                            {{ $a->icon }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="truncate font-bold {{ $done ? 'text-gray-900' : 'text-gray-500' }}">
                                    {{ $a->name }}
                                </h3>

                                <span class="shrink-0 text-sm">{{ $done ? '✓' : '🔒' }}</span>
                            </div>

                            <p class="mt-1 text-xs leading-relaxed {{ $done ? 'text-gray-500' : 'text-gray-400' }}">
                                {{ $a->description }}
                            </p>

                            @php $rar = $rarityMap[$a->rarity ?? 'common']; @endphp
                            <span class="mt-2 inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $rar[1] }}">
                                {{ $rar[0] }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        @if ($done)
                            <p class="text-xs font-semibold text-green-700">
                                +{{ $a->xp_reward }} XP · terbuka {{ $row['unlocked_at']->format('d M Y') }}
                            </p>
                        @else
                            <div class="mb-1 flex justify-between text-[11px] text-gray-400">
                                <span>Progres</span>
                                <span>{{ $row['current'] }}/{{ $a->target }}</span>
                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                                <div class="h-full rounded-full bg-green-400" style="width: {{ $row['pct'] }}%"></div>
                            </div>

                            <p class="mt-2 text-[11px] text-gray-400">Hadiah: +{{ $a->xp_reward }} XP</p>
                        @endif
                    </div>

                </div>
            @endforeach

        </div>

    </div>
@endsection
