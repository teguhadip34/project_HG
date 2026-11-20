@extends('layouts.app')

@section('title', 'Riwayat Scan - EcoScan AI')

@section('header')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-800 via-green-700 to-emerald-600 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="pointer-events-none absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="relative">
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Riwayat Scan</h1>
            <p class="mt-2 max-w-xl text-sm text-green-50 sm:text-base">
                Semua sampah yang pernah kamu pindai, lengkap dengan hasil identifikasi AI.
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        {{-- FILTER KATEGORI --}}
        @php
            $tabs = collect(['' => 'Semua'])
                ->union($categories->mapWithKeys(fn ($c) => [$c => $c]))
                ->union(['tidak-dikenali' => 'Tidak dikenali']);
        @endphp

        <div class="flex flex-wrap gap-2">
            @foreach ($tabs as $value => $label)
                @php $active = (string) $kategori === (string) $value; @endphp
                <a href="{{ $value === '' ? route('history') : route('history', ['kategori' => $value]) }}"
                   class="rounded-full px-4 py-1.5 text-sm font-semibold transition
                          {{ $active ? 'bg-green-700 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-green-300' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- DAFTAR SCAN --}}
        @if ($scans->count())
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($scans as $scan)
                    <a href="{{ route('scan.show', $scan) }}"
                       class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                        <div class="aspect-square overflow-hidden bg-gray-100">
@if ($scan->image_data)
    <img src="{{ $scan->image_data }}"
         alt="{{ $scan->wasteType->name ?? 'Tidak dikenali' }}"
         loading="lazy"
         class="h-full w-full object-cover transition group-hover:scale-105">
@else
    <div class="flex h-full w-full items-center justify-center text-xs text-gray-400">Tanpa foto</div>
@endif
                        </div>

                        <div class="space-y-2 p-4">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="truncate font-bold text-gray-900">
                                    {{ $scan->wasteType->name ?? 'Tidak dikenali' }}
                                </h3>
                                <span class="shrink-0 rounded-full bg-green-100 px-2.5 py-0.5 text-[11px] font-bold text-green-700">
                                    {{ $scan->wasteType->category->name ?? '-' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>
                                    @if ($scan->confidence !== null)
                                        Keyakinan {{ rtrim(rtrim(number_format($scan->confidence, 1), '0'), '.') }}%
                                    @else
                                        -
                                    @endif
                                </span>
                                @if ($scan->xp_earned > 0)
                                    <span class="font-semibold text-green-700">+{{ $scan->xp_earned }} XP</span>
                                @endif
                            </div>

                            <p class="text-[11px] text-gray-400">{{ $scan->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div>{{ $scans->links() }}</div>
        @else
            <div class="rounded-3xl border border-dashed border-gray-200 bg-white p-10 text-center">
                <p class="text-4xl">♻️</p>
                <p class="mt-3 font-bold text-gray-800">Belum ada scan di sini</p>
                <p class="mt-1 text-sm text-gray-500">Mulai scan sampah pertamamu untuk melihat riwayatnya.</p>
                <a href="{{ route('scan') }}"
                   class="mt-4 inline-block rounded-full bg-green-700 px-5 py-2 text-sm font-semibold text-white hover:bg-green-800">
                    Scan Sekarang
                </a>
            </div>
        @endif
    </div>
@endsection
