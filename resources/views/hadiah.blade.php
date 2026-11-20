@extends('layouts.app')

@section('title', 'Eco Rewards - EcoScan AI')

@section('header')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-800 via-green-700 to-emerald-600 px-6 py-7 text-white shadow-lg sm:px-8">
        <div class="pointer-events-none absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">🎁 Eco Rewards</h1>
                <p class="mt-2 max-w-xl text-sm text-green-50 sm:text-base">
                    Tukarkan XP dari hasil scan dengan hadiah ramah lingkungan.
                </p>
            </div>

            <div class="rounded-2xl bg-white/15 px-5 py-3 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wider text-green-100">Saldo XP</p>
                <p class="text-3xl font-extrabold">{{ number_format($balance) }}</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        @if (session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                🎉 {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($isGuest)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Kamu masuk sebagai <strong>Guest</strong>. Daftar akun dulu untuk bisa menukar hadiah.
            </div>
        @endif

        <p class="text-sm text-gray-500">
            Menukar hadiah hanya mengurangi saldo. Total XP ({{ number_format($totalXp) }}), level, dan peringkatmu tidak turun.
        </p>

        {{-- DAFTAR HADIAH --}}
        @if ($rewards->isEmpty())
            <div class="rounded-3xl border border-dashed border-gray-200 bg-white p-10 text-center">
                <p class="text-4xl">🎁</p>
                <p class="mt-3 font-bold text-gray-800">Belum ada hadiah tersedia</p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($rewards as $reward)
                    @php
                        $out      = $reward->isOutOfStock();
                        $enough   = $balance >= $reward->cost_xp;
                        $canRedeem = ! $isGuest && ! $out && $enough;
                        $missing  = max(0, $reward->cost_xp - $balance);
                    @endphp

                    <div class="flex flex-col rounded-2xl border p-5 shadow-sm
                                {{ $canRedeem ? 'border-green-200 bg-white' : 'border-gray-100 bg-gray-50' }}">

                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-3xl
                                        {{ $canRedeem ? 'bg-green-100' : 'bg-gray-200 opacity-60' }}">
                                {{ $reward->icon }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-gray-900">{{ $reward->name }}</h3>
                                <p class="mt-1 text-xs leading-relaxed text-gray-500">{{ $reward->description }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="font-bold text-green-700">{{ number_format($reward->cost_xp) }} XP</span>
                            @if ($reward->stock !== null)
                                <span class="{{ $out ? 'font-semibold text-red-500' : 'text-gray-400' }}">
                                    {{ $out ? 'Stok habis' : 'Sisa ' . $reward->stock }}
                                </span>
                            @endif
                        </div>

                        <form action="{{ route('rewards.redeem', $reward) }}" method="POST" class="mt-4"
                              onsubmit="return confirm('Tukar {{ addslashes($reward->name) }} seharga {{ $reward->cost_xp }} XP?')">
                            @csrf
                            <button type="submit" @disabled(! $canRedeem)
                                    class="w-full rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                           {{ $canRedeem ? 'bg-green-700 text-white hover:bg-green-800' : 'cursor-not-allowed bg-gray-200 text-gray-400' }}">
                                @if ($isGuest)
                                    Daftar untuk menukar
                                @elseif ($out)
                                    Stok habis
                                @elseif (! $enough)
                                    Kurang {{ number_format($missing) }} XP
                                @else
                                    Tukar Sekarang
                                @endif
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- RIWAYAT PENUKARAN --}}
        <div class="rounded-3xl border border-green-100 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-extrabold text-gray-900">Riwayat Penukaran</h2>

            @forelse ($redemptions as $r)
                <div class="flex items-center gap-4 border-b border-gray-100 py-3 last:border-0">
                    <span class="text-2xl">{{ $r->reward->icon ?? '🎁' }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold text-gray-900">{{ $r->reward->name ?? 'Hadiah' }}</p>
                        <p class="text-xs text-gray-400">{{ $r->created_at->format('d M Y, H:i') }} · -{{ number_format($r->cost_xp) }} XP</p>
                    </div>
                    <code class="rounded-lg bg-green-50 px-3 py-1 text-xs font-bold text-green-800">{{ $r->code }}</code>
                </div>
            @empty
                <p class="mt-3 text-sm text-gray-500">Belum ada penukaran. Kumpulkan XP dengan scan sampah!</p>
            @endforelse
        </div>
    </div>
@endsection
