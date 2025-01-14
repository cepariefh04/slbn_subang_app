<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\JumlahAset;
use App\Models\Tahun;
use App\Services\MonteCarloPrediction;
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

  public function prediksi()
  {
    $assets = Aset::get();

    return view('sarpras.prediksi', [
      'assets' => $assets,
    ]);
  }

  public function predict2024()
  {
    $data = [
      2019 => 196,
      2020 => 196,
      2021 => 196,
      2022 => 235,
      2023 => 235,
    ];

    $monteCarlo = new MonteCarloPrediction();
    $prediction = $monteCarlo->predict($data, 8, 5, 99, 13);

    return response()->json(['prediction' => $prediction]);
  }
}
