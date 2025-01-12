<?php

namespace App\Http\Controllers;

use App\Models\JumlahAset;
use App\Models\Tahun;
use Illuminate\Http\Request;

class SarprasController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->input('year'); // Tahun terpilih
        $selectedShow = $request->input('show', 10); // Jumlah data (default: 10)

        $years = Tahun::orderBy('created_at', 'DESC')->get();

        // Query untuk assets
        $query = JumlahAset::with(['tahun', 'aset']);

        // Filter berdasarkan tahun jika ada
        if ($selectedYear) {
            $query->whereHas('tahun', function ($q) use ($selectedYear) {
                $q->where('tahun', $selectedYear);
            });
        }

        // Tentukan jumlah data yang akan ditampilkan
        if ($selectedShow === 'all') {
            $assets = $query->get(); // Tampilkan semua data
        } else {
            $assets = $query->paginate((int) $selectedShow); // Paginate sesuai pilihan
        }

        return view('sarpras.index', [
            'assets' => $assets,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'selectedShow' => $selectedShow,
        ]);
    }
}
