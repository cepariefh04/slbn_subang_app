<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\JumlahAset;
use App\Models\Tahun;
use App\Services\MonteCarloPrediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function prosesPrediksi()
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
                $a = \random_int(1, 999);
                $c = \random_int(1, 999);
                $m = \random_int(1, 999);
                $x0 = \random_int(1, 999);
                $predictedValue = $monteCarlo->predict($data, $a, $c, $m, $x0);
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
            Log::info('Tahun ID yang digunakan: ' . $tahunRecord->id);
            Log::info('data', $validated['predictions']);
            // Loop melalui prediksi dan simpan ke tabel 'jumlah_asets'
            foreach ($validated['predictions'] as $prediction) {
                Log::info('Menyimpan prediksi:', [
                    'tahun_id' => $tahunRecord->id,
                    'aset_id' => (int) $prediction['aset_id'],
                    'predicted_value' => (int) $prediction['predicted_value']
                ]);

                $asetId = $prediction['aset_id'];
                $predictedValue = $prediction['predicted_value'];

                if (in_array($asetId, [1, 2])) {
                    // Jika aset_id adalah 1 atau 2, masukkan ke kolom 'jumlah'
                    JumlahAset::create(
                        [
                            'tahun_id' => $tahunRecord->id,
                            'aset_id' => $asetId,
                            'jumlah' => $predictedValue,
                            'jumlah_layak' => 0,
                            'jumlah_tidak_layak' => 0, // Default
                        ]
                    );
                } else {
                    // Selain itu, masukkan ke kolom 'jumlah_tidak_layak'
                    JumlahAset::create(
                        [
                            'tahun_id' => $tahunRecord->id,
                            'aset_id' => $asetId,
                            'jumlah' => 0, // Default
                            'jumlah_layak' => 0,
                            'jumlah_tidak_layak' => $predictedValue,
                        ]
                    );
                }
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
}
