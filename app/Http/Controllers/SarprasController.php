<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\JumlahAset;
use App\Models\JumlahPeserta;
use App\Models\RiwayatPrediksi;
use App\Models\Tahun;
use App\Services\MonteCarloPrediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SarprasController extends Controller
{
    public function index()
    {
        $years = Tahun::orderBy('created_at', 'DESC')->get();
        $assets = JumlahAset::with(['tahun', 'aset'])->get();

        return view('sarpras.index', [
            'assets' => $assets,
            'years' => $years,
        ]);
    }

    public function dataPeserta()
    {
        $years = Tahun::orderBy('created_at', 'DESC')->get();
        $peserta = JumlahPeserta::with(['tahun'])->get();

        return view('sarpras.dataPeserta', [
            'years' => $years,
            'peserta' => $peserta
        ]);
    }

    public function prediksi(Request $request)
    {
        $selectedYear = $request->input('year');

        if ($selectedYear) {
            $riwayat = RiwayatPrediksi::whereHas('tahun', function ($q) use ($selectedYear) {
                $q->where('tahun', $selectedYear);
            })
                ->with(['tahun'])
                ->get();

            // Cari tahun terakhir sebelum yang dipilih
            $lastYear = Tahun::where('tahun', '<', $selectedYear)
                ->orderByDesc('tahun')
                ->first();

            $lastYearPredicted = false;

            if ($lastYear) {
                // Cek apakah tahun terakhir sudah memiliki prediksi
                $lastYearPredicted = JumlahAset::whereHas('tahun', function ($q) use ($lastYear) {
                    $q->where('tahun', $lastYear->tahun);
                })->exists();
            }
        } else {
            $riwayat = RiwayatPrediksi::with(['tahun', 'aset'])->get();
            $lastYearPredicted = true; // Jika tidak ada tahun yang dipilih, biarkan tetap true
        }

        $assets = Aset::get();
        $years = Tahun::get();

        return view('sarpras.prediksi', [
            'assets' => $assets,
            'years' => $years,
            'riwayats' => $riwayat,
            'selectedYear' => $selectedYear,
            'lastYearPredicted' => $lastYearPredicted,
            'lastYear' => $lastYear->tahun ?? null,
        ]);
    }


    public function prosesPrediksi()
    {
        $selectedYear = session('selectedYear');

        if (!$selectedYear) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun belum dipilih.',
            ], 400);
        }

        // Ambil data aset berdasarkan kondisi
        $allAsetData = $this->getAsetData($selectedYear);

        // Inisialisasi algoritma Monte Carlo
        $monteCarlo = new MonteCarloPrediction();

        $predictions = [];

        // Lakukan prediksi untuk setiap aset
        foreach ($allAsetData as $asetName => $data) {
            // Periksa apakah kolom 'jumlah_tidak_layak' selalu 0 untuk aset
            if ($this->isJumlahTidakLayakZero($data)) {
                // Jika selalu 0, kembalikan hasil 0
                $predictions[$asetName] = 0;
            } else {
                $a = \random_int(1, 999);
                $c = \random_int(1, 999);
                $m = \random_int(1, 999);
                $x0 = \random_int(1, 999);
                $predictedValue = $monteCarlo->predict($data, $a, $c, $m, $x0);
                $predictions[$asetName] = $predictedValue;
            }
        }

        // Tampilkan hasil prediksi
        return response()->json([
            'success' => true,
            'predictions' => $predictions,
        ]);
    }

    private function getAsetData(string $selectedYear): array
    {
        // Ambil 5 tahun terakhir sebelum tahun terbaru
        $pastYears = Tahun::where('tahun', '<', $selectedYear)
            ->orderByDesc('tahun')
            ->limit(5)
            ->pluck('id');

        // Ambil data jumlah aset berdasarkan 5 tahun terakhir
        $allData = JumlahAset::join('asets', 'jumlah_asets.aset_id', '=', 'asets.id')
            ->join('tahuns', 'jumlah_asets.tahun_id', '=', 'tahuns.id')
            ->whereIn('jumlah_asets.tahun_id', $pastYears)
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
            // $column = in_array($asetName, $asetJumlah) ? 'jumlah' : 'jumlah_tidak_layak';

            // Ambil data berdasarkan kolom yang sesuai
            $results[$asetName] = $data->pluck('jumlah', 'tahun')->toArray();
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

    public function store(Request $request)
    {
        try {
            // Validasi data
            $validated = $request->validate([
                'tahun' => 'required|string',
                'predictions' => 'required|array',
                'predictions.*.aset_id' => 'required|integer',
                'predictions.*.predicted_value' => 'required|numeric',
            ]);

            // Pastikan data tahun ada di tabel 'tahuns'
            $tahun = $validated['tahun'];
            $tahunRecord = Tahun::firstOrCreate(['tahun' => $tahun]);

            // Loop melalui prediksi dan simpan ke tabel 'jumlah_asets'
            foreach ($validated['predictions'] as $prediction) {

                $asetId = $prediction['aset_id'];
                $predictedValue = $prediction['predicted_value'];

                // if (in_array($asetId, [1, 2])) {
                //   JumlahAset::create(
                //     [
                //       'tahun_id' => $tahunRecord->id,
                //       'aset_id' => $asetId,
                //       'jumlah' => $predictedValue,
                //       'jumlah_layak' => 0,
                //       'jumlah_tidak_layak' => 0, // Default
                //     ]
                //   );
                // } else {
                //   // Selain itu, masukkan ke kolom 'jumlah_tidak_layak'
                //   JumlahAset::create(
                //     [
                //       'tahun_id' => $tahunRecord->id,
                //       'aset_id' => $asetId,
                //       'jumlah' => 0, // Default
                //       'jumlah_layak' => 0,
                //       'jumlah_tidak_layak' => $predictedValue,
                //     ]
                //   );
                // }

                RiwayatPrediksi::create(
                    [
                        'tahun_id' => $tahunRecord->id,
                        'aset_id' => $asetId,
                        'jumlah' => $predictedValue,
                        'jumlah_layak' => 0,
                        'jumlah_tidak_layak' => 0, // Default
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Hasil prediksi berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error menyimpan prediksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan prediksi.',
            ], 500);
        }
    }

    public function pengajuan(Request $request)
    {
        $years = Tahun::orderBy('created_at', 'DESC')->get();

        // Query untuk assets
        $riwayat = RiwayatPrediksi::with(['aset'])->get();
        foreach ($riwayat as $item) {
            $lastTahunId = $item->tahun_id - 1;
        }
        $aset = JumlahAset::where('tahun_id', $lastTahunId)->with(['tahun'])->get();
        $peserta = JumlahPeserta::with(['tahun'])->get();

        return view('sarpras.pengajuan', [
            'riwayat' => $riwayat,
            'years' => $years,
            'lastData' => $aset,
            // 'selectedYear' => $selectedYear,
            // 'selectedShow' => $selectedShow,
            'peserta' => $peserta
        ]);
    }
}
