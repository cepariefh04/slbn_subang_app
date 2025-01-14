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

    // public function predict2024()
    // {
    //     $data = [
    //         2019 => 2,
    //         2020 => 2,
    //         2021 => 0,
    //         2022 => 2,
    //         2023 => 2,
    //     ];

    //     $monteCarlo = new MonteCarloPrediction();
    //     $prediction = $monteCarlo->predict($data, 8, 5, 99, 13);

    //     return response()->json(['prediction' => $prediction]);
    // }

    public function predict()
    {
        // Aset yang menggunakan kolom 'jumlah'
        $asetJumlah = ['Kursi Siswa', 'Meja Siswa'];

        // Ambil data aset berdasarkan kondisi
        $allAsetData = $this->getAsetData($asetJumlah);
        // dd($allAsetData);
        // Inisialisasi algoritma Monte Carlo
        $monteCarlo = new MonteCarloPrediction();

        $predictions = [];

        // Lakukan prediksi untuk setiap aset
        foreach ($allAsetData as $asetName => $data) {
            // Periksa apakah kolom 'jumlah_tidak_layak' selalu 0 untuk aset
            // dd($asetName);
            if ($this->isJumlahTidakLayakZero($data)) {
                // Jika selalu 0, kembalikan hasil 0
                $predictions[$asetName] = 0;
            } else {
                // Jika tidak, lakukan prediksi dengan Monte Carlo
                $column = in_array($asetName, $asetJumlah) ? 'jumlah' : 'jumlah_tidak_layak';
                $predictedValue = $monteCarlo->predict($data, 8, 5, 99, 13);
                $predictions[$asetName] = $predictedValue;
            }
        }
        // dd($predictions);
        // Tampilkan hasil prediksi
        return response()->json([
            'success' => true,
            'predictions' => $predictions,
        ]);
    }

    private function getAsetData(array $asetJumlah): array
    {
        // Ambil semua data jumlah aset sekaligus
        $allData = JumlahAset::join('asets', 'jumlah_asets.aset_id', '=', 'asets.id')
            ->join('tahuns', 'jumlah_asets.tahun_id', '=', 'tahuns.id')
            ->select(
                'asets.nama as aset_name',
                'tahuns.tahun',
                'jumlah_asets.jumlah',
                'jumlah_asets.jumlah_tidak_layak'
            )
            ->orderBy('asets.nama')
            ->orderBy('tahuns.tahun')
            ->get();

        // Kelompokkan data berdasarkan aset
        $groupedData = $allData->groupBy('aset_name');

        $results = [];

        foreach ($groupedData as $asetName => $data) {
            // Tentukan kolom yang digunakan
            $column = in_array($asetName, $asetJumlah) ? 'jumlah' : 'jumlah_tidak_layak';

            // Ambil data berdasarkan kolom yang sesuai
            $results[$asetName] = $data->pluck($column, 'tahun')->toArray();
        }

        return $results;
    }

    private function isJumlahTidakLayakZero(array $data): bool
    {
        // Periksa apakah setiap nilai dalam data adalah 0
        foreach ($data as $year => $value) {
            if ($value != 0) {
                return false; // Jika ada nilai yang tidak 0, kembalikan false
            }
        }
        return true; // Jika semua nilai adalah 0, kembalikan true
    }
}
