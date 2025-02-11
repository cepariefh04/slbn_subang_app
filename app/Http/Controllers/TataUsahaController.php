<?php

namespace App\Http\Controllers;

use App\Models\JumlahAset;
use App\Models\JumlahPeserta;
use App\Models\Tahun;
use App\Services\MonteCarloPrediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TataUsahaController extends Controller
{
    public function index()
    {
        $peserta = JumlahPeserta::with(['tahun'])->get();
        $assets = JumlahAset::with(['tahun', 'aset'])->get();
        return view('tata-usaha.index', [
            'peserta' => $peserta,
            'assets' => $assets,
        ]);
    }

    public function dataAset()
    {
        $assets = JumlahAset::with(['tahun', 'aset'])->get();
        return view('tata-usaha.dataAset', [
            'assets' => $assets,
        ]);
    }

    public function prediksi(Request $request)
    {

        $selectedYear = $request->input('year');
        if ($selectedYear) {
            $hasilPrediksi = JumlahPeserta::whereHas('tahun', function ($q) use ($selectedYear) {
                $q->where('tahun', $selectedYear);
            })->with(['tahun'])->get();

            // Cari tahun terakhir sebelum yang dipilih
            $lastYear = Tahun::where('tahun', '<', $selectedYear)
                ->orderByDesc('tahun')
                ->first();

            $lastYearPredicted = false;

            if ($lastYear) {
                // Cek apakah tahun terakhir sudah memiliki prediksi
                $lastYearPredicted = JumlahPeserta::whereHas('tahun', function ($q) use ($lastYear) {
                    $q->where('tahun', $lastYear->tahun);
                })->exists();
            }
        } else {
            $lastYearPredicted = true;
            $hasilPrediksi = collect();
        }

        $years = Tahun::get();

        return view('tata-usaha.prediksi', [
            'years' => $years,
            'selectedYear' => $selectedYear,
            'lastYearPredicted' => $lastYearPredicted,
            'lastYear' => $lastYear->tahun ?? null,
            'hasilPrediksi' => $hasilPrediksi
        ]);
    }

    public function prosesPrediksi($tahun)
    {
        // Ambil data
        $pastYears = Tahun::where('tahun', '<', $tahun)
            ->orderByDesc('tahun')
            ->limit(5)
            ->pluck('id');

        $dataPeserta = JumlahPeserta::join('tahuns', 'jumlah_pesertas.tahun_id', '=', 'tahuns.id')
            ->whereIn('jumlah_pesertas.tahun_id', $pastYears)
            ->select(
                'tahuns.tahun as tahun',
                'jumlah_pesertas.laki_laki',
                'jumlah_pesertas.perempuan'
            )
            ->orderBy('tahuns.tahun')
            ->get();

        // Siapkan data laki-laki dan perempuan dengan tahun sebagai kunci
        $dataLakiLaki = $dataPeserta->pluck('laki_laki', 'tahun')->toArray();
        $dataPerempuan = $dataPeserta->pluck('perempuan', 'tahun')->toArray();

        // Inisialisasi parameter Monte Carlo
        $monteCarlo = new MonteCarloPrediction();
        $a = \random_int(1, 999);
        $c = \random_int(1, 999);
        $m = \random_int(1, 999);
        $x0 = \random_int(1, 999);

        // Prediksi data laki-laki dan perempuan
        $predictedLakiLaki = $monteCarlo->predict($dataLakiLaki, $a, $c, $m, $x0);
        $predictedPerempuan = $monteCarlo->predict($dataPerempuan, $a, $c, $m, $x0);

        // Pastikan hasil prediksi berupa array
        if (!is_array($predictedLakiLaki)) {
            $predictedLakiLaki = [$predictedLakiLaki];
        }
        if (!is_array($predictedPerempuan)) {
            $predictedPerempuan = [$predictedPerempuan];
        }

        // Tambahkan hasil prediksi
        $totalPredicted = array_map(function ($laki, $perempuan) {
            return $laki + $perempuan;
        }, $predictedLakiLaki, $predictedPerempuan);

        // Tampilkan hasil prediksi
        return response()->json([
            'success' => true,
            'predictions' => [
                'laki_laki' => $predictedLakiLaki,
                'perempuan' => $predictedPerempuan,
                'total' => $totalPredicted,
            ],
        ]);
    }

    public function simpanPrediksi(Request $request)
    {
        try {
            $validated = $request->validate([
                'laki_laki' => 'required|integer',
                'perempuan' => 'required|integer',
                'total' => 'required|integer',
                'tahun' => 'required|string'
            ]);

            $selectedYear =  $validated['tahun'];
            $tahunArray = explode('-', $selectedYear);
            $selectedYear = ($tahunArray[0] + 1) . '-' . ($tahunArray[1] + 1);

            // Ambil tahun_id terakhir dari tabel tahuns
            $tahun = Tahun::latest('id')->first();
            if (!$tahun) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun tidak ditemukan.',
                ], 404);
            }

            // Simpan data ke tabel jumlah_pesertas
            JumlahPeserta::create([
                'tahun_id' => $tahun->id,
                'laki_laki' => $validated['laki_laki'],
                'perempuan' => $validated['perempuan'],
                'jumlah' => $validated['total'],
            ]);

            Tahun::create(['tahun' => $selectedYear]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan ke database.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan data:', ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updatePeserta(Request $request, $id)
    {
        try {
            $jumlahPeserta = JumlahPeserta::findOrFail($id);

            $request->validate([
                'laki_laki' => 'required|integer',
                'perempuan' => 'required|integer',
                'jumlah' => 'required|integer',
            ]);

            $jumlahPeserta->laki_laki = $request->laki_laki;
            $jumlahPeserta->perempuan = $request->perempuan;
            $jumlahPeserta->jumlah = $request->jumlah;
            $jumlahPeserta->save();

            return redirect()->back()->with('success', 'Pengguna berhasil diperbarui!');

            // return redirect()->route('admin.dashboard')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan pengguna. Silakan coba lagi.');
        }
    }
}
