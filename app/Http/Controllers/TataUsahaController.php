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

        return view('tata-usaha.index', [
            'peserta' => $peserta,
        ]);
    }

    public function prediksi()
    {
        return view('tata-usaha.prediksi');
    }

    public function prosesPrediksi()
    {
        // Ambil data
        $dataPeserta = JumlahPeserta::join('tahuns', 'jumlah_pesertas.tahun_id', '=', 'tahuns.id')
            ->select(
                'tahuns.tahun as tahun',
                'jumlah_pesertas.laki_laki',
                'jumlah_pesertas.perempuan'
            )
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
        Log::debug('Data yang diterima:', $request->all());

        try {
            $validated = $request->validate([
                'laki_laki' => 'required|integer',
                'perempuan' => 'required|integer',
                'total' => 'required|integer',
            ]);

            Log::debug('Data setelah validasi:', $validated);

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
}
