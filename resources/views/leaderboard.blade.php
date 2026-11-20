@extends('layouts.app')

@section('title', 'Peringkat - EcoScan AI')

@section('header')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-800 via-green-700 to-emerald-600 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="pointer-events-none absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="relative">
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">🏆 Papan Peringkat</h1>
            <p class="mt-2 max-w-xl text-sm text-green-50 sm:text-base">
                Siapa yang paling rajin memilah sampah? Peringkat dihitung dari scan yang berhasil dikenali.
            </p>
        </div>
    </div>
@endsection

@section('content')
    @php
        $medals = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
        $unit   = $metric === 'xp' ? 'XP' : 'scan';
    @endphp

    <div class="space-y-6">

        {{-- FILTER PERIODE & URUTAN --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-2">
                @foreach ($periods as $value => $label)
                    <a href="{{ route('leaderboard', ['periode' => $value, 'urut' => $metric]) }}"
                       class="rounded-full px-4 py-1.5 text-sm font-semibold transition
                              {{ $period === $value ? 'bg-green-700 text-white' : 'border border-gray-200 bg-white text-gray-600 hover:border-green-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="inline-flex rounded-full border border-gray-200 bg-white p-1 text-sm font-semibold">
                <a href="{{ route('leaderboard', ['periode' => $period, 'urut' => 'scan']) }}"
                   class="rounded-full px-4 py-1 {{ $metric === 'scan' ? 'bg-green-700 text-white' : 'text-gray-600' }}">Jumlah Scan</a>
                <a href="{{ route('leaderboard', ['periode' => $period, 'urut' => 'xp']) }}"
                   class="rounded-full px-4 py-1 {{ $metric === 'xp' ? 'bg-green-700 text-white' : 'text-gray-600' }}">XP</a>
            </div>
        </div>

        @if ($rows->isEmpty())
            <div class="rounded-3xl border border-dashed border-gray-200 bg-white p-10 text-center">
                <p class="text-4xl">🌱</p>
                <p class="mt-3 font-bold text-gray-800">Belum ada yang scan di periode ini</p>
                <p class="mt-1 text-sm text-gray-500">Jadilah yang pertama di papan peringkat!</p>
                <a href="{{ route('scan') }}"
                   class="mt-4 inline-block rounded-full bg-green-700 px-5 py-2 text-sm font-semibold text-white hover:bg-green-800">
                    Scan Sekarang
                </a>
            </div>
        @else
            {{-- DAFTAR PERINGKAT --}}
            <div class="overflow-hidden rounded-3xl border border-green-100 bg-white shadow-sm">
                <ol class="divide-y divide-gray-100">
                    @foreach ($rows as $row)
                        @php $isMe = $row->id === $myId; @endphp
                        <li class="flex items-center gap-4 px-5 py-4 {{ $isMe ? 'bg-green-50' : '' }}">
                            <div class="w-10 shrink-0 text-center text-xl font-extrabold {{ $row->rank <= 3 ? '' : 'text-gray-400 text-base' }}">
                                {{ $medals[$row->rank] ?? '#' . $row->rank }}
                            </div>

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 font-bold text-green-800">
                                {{ strtoupper(mb_substr($row->name, 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate font-bold text-gray-900">
                                    {{ $row->name }}
                                    @if ($isMe)
                                        <span class="ml-1 rounded-full bg-green-600 px-2 py-0.5 text-[10px] font-bold text-white">Kamu</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $metric === 'xp' ? $row->scan_count . ' scan' : number_format($row->period_xp) . ' XP' }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-xl font-extrabold text-green-800">
                                    {{ number_format($metric === 'xp' ? $row->period_xp : $row->scan_count) }}
                                </p>
                                <p class="text-[11px] text-gray-400">{{ $unit }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- POSISI KAMU (jika di luar daftar) --}}
            @if ($me)
                <div class="flex items-center gap-4 rounded-2xl border border-green-200 bg-green-50 px-5 py-4">
                    <div class="w-10 shrink-0 text-center text-base font-extrabold text-gray-500">#{{ $me->rank }}</div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-gray-900">Posisimu</p>
                        <p class="text-xs text-gray-500">{{ $me->scan_count }} scan · {{ number_format($me->period_xp) }} XP</p>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
