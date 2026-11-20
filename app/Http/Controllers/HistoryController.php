<?php

namespace App\Http\Controllers;

use App\Models\WasteCategory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->query('kategori');

        $scans = $request->user()->scans()
            ->with('wasteType.category')
            ->when($kategori === 'tidak-dikenali', fn ($q) => $q->whereNull('waste_type_id'))
            ->when(
                $kategori && $kategori !== 'tidak-dikenali',
                fn ($q) => $q->whereHas('wasteType.category', fn ($c) => $c->where('name', $kategori))
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = WasteCategory::orderBy('name')->pluck('name');

        return view('riwayat', compact('scans', 'categories', 'kategori'));
    }
}
