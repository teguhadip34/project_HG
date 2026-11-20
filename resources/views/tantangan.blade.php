@extends('layouts.app')

@section('title', 'Tantangan - EcoScan AI')

@section('header')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-800 via-green-700 to-emerald-600 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="pointer-events-none absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="relative">
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">🎯 Tantangan</h1>
            <p class="mt-2 max-w-xl text-sm text-green-50 sm:text-base">
                Selesaikan tantangan harian dan mingguan untuk bonus XP.
            </p>
        </div>
    </div>
@endsection

@section('content')
    @php
        $sections = [
            'daily'  => ['Tantangan Harian', '☀️'],
            'weekly' => ['Tantangan Mingguan', '📅'],
        ];
    @endphp

    <div class="space-y-8">
        @foreach ($sections as $key => [$title, $emoji])
            @php $items = $board[$key] ?? []; @endphp

            @if (count($items))
                <section>
                    <div class="mb-3 flex items-end justify-between">
                        <h2 class="text-xl font-extrabold text-gray-900">{{ $emoji }} {{ $title }}</h2>
                        <p class="text-xs text-gray-500">Reset dalam {{ $items[0]['remaining'] }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($items as $ch)
                            <div class="rounded-2xl border p-5 shadow-sm {{ $ch['done'] ? 'border-green-200 bg-white' : 'border-gray-100 bg-white' }}">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-3xl
                                                {{ $ch['done'] ? 'bg-green-100' : 'bg-gray-100' }}">
                                        {{ $ch['icon'] }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <h3 class="font-bold text-gray-900">{{ $ch['name'] }}</h3>
                                            <span class="shrink-0 text-sm">{{ $ch['done'] ? '✅' : '' }}</span>
                                        </div>
                                        <p class="mt-1 text-xs font-semibold text-green-700">Hadiah +{{ $ch['xp_reward'] }} XP</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="mb-1 flex justify-between text-[11px] text-gray-500">
                                        <span>{{ $ch['done'] ? 'Selesai!' : 'Progres' }}</span>
                                        <span>{{ $ch['current'] }}/{{ $ch['target'] }}</span>
                                    </div>
                                    <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                        <div class="h-full rounded-full {{ $ch['done'] ? 'bg-emerald-500' : 'bg-green-400' }}"
                                             style="width: {{ $ch['pct'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <p class="text-xs text-gray-400">
            Hanya scan yang berhasil dikenali dan menghasilkan XP yang dihitung. Foto yang sama tidak dihitung dua kali.
        </p>
    </div>
@endsection
