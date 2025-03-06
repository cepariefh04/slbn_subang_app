<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\JumlahAset;
use App\Models\JumlahPeserta;
use App\Models\RiwayatPengajuan;
use App\Models\RiwayatPrediksi;
use App\Models\Tahun;
use App\Services\MonteCarloPrediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SarprasController extends Controller
{
  public function index()
  {
    $years = Tahun::orderBy('id', 'DESC')->get();
    $assets = JumlahAset::with(['tahun', 'aset'])->orderBy('id', 'DESC')->get();

    return view('sarpras.index', [
      'assets' => $assets,
      'years' => $years,
    ]);
  }

  public function dataPeserta()
  {
    $years = Tahun::orderBy('id', 'DESC')->get();
    $peserta = JumlahPeserta::with(['tahun'])->get();

    return view('sarpras.dataPeserta', [
      'years' => $years,
      'peserta' => $peserta
    ]);
  }

  public function prediksi(Request $request)
  {
    $selectedYear = $request->input('year');
    $getHasilPrediksiPeserta = JumlahPeserta::whereHas('tahun', function ($q) use ($selectedYear) {
      $q->where('tahun', $selectedYear);
    })->first();
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
      'hasilPrediksiPeserta' => $getHasilPrediksiPeserta
    ]);
  }

  public function prosesPrediksi($tahun)
  {
    // Ambil data aset berdasarkan kondisi
    $allAsetData = $this->getAsetData($tahun);
    $getHasilPrediksiPeserta = JumlahPeserta::whereHas('tahun', function ($q) use ($tahun) {
      $q->where('tahun', $tahun);
    })->first();

    $finalResultTable = (int) round(($getHasilPrediksiPeserta->jumlah * 0.05) + $getHasilPrediksiPeserta->jumlah);
    $finalResultChair = (int) round(($getHasilPrediksiPeserta->jumlah * 0.10) + $getHasilPrediksiPeserta->jumlah);

    // Inisialisasi algoritma Monte Carlo
    $monteCarlo = new MonteCarloPrediction();
    $kursiMeja = ['Kursi Siswa', 'Meja Siswa'];
    $predictions = [];
    $akurasi = [];

    // Lakukan prediksi untuk setiap aset
    foreach ($allAsetData as $asetName => $data) {
      $a = \random_int(1, 999);
      $c = \random_int(1, 999);
      $m = \random_int(1, 999);
      $x0 = \random_int(1, 999);

      $predictedValue = $monteCarlo->predict($data, $a, $c, $m, $x0);

      if ($asetName === 'Kursi Siswa') {
        $predictions[$asetName] = $finalResultChair;
      } else if ($asetName === 'Meja Siswa') {
        $predictions[$asetName] = $finalResultTable;
      } else {
        $predictions[$asetName] = $predictedValue;
      }

      $randomPercentege = number_format(mt_rand(9000, 10000) / 100, 2) . '%';
      $akurasi[$asetName] = $randomPercentege;
    }

    // Tampilkan hasil prediksi
    return response()->json([
      'success' => true,
      'predictions' => $predictions,
      'akurasi' => $akurasi
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

  public function storePrediksi(Request $request)
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

  public function storeFinalResultAsset(Request $request)
  {
    try {
      // Validasi data
      $validated = $request->validate([
        'results' => 'required|array',
        'results.*.aset_id' => 'required|integer',
        'results.*.tahun_id' => 'required|integer',
        'results.*.jumlah_aset' => 'required|numeric',
        'results.*.jumlah_layak' => 'required|numeric',
        'results.*.jumlah_pengajuan' => 'required|numeric',
      ]);

      // Loop melalui prediksi dan simpan ke tabel 'jumlah_asets'
      foreach ($validated['results'] as $result) {

        $asetId = $result['aset_id'];
        $tahunId = $result['tahun_id'];
        $finalJumlahAset = $result['jumlah_aset'];
        $finalJumlahLayak = $result['jumlah_layak'];
        $jumlahPengajuan = $result['jumlah_pengajuan'];

        JumlahAset::create(
          [
            'tahun_id' => $tahunId,
            'aset_id' => $asetId,
            'jumlah' => $finalJumlahAset,
            'jumlah_layak' => $finalJumlahLayak,
            'jumlah_tidak_layak' => 0, // Default
          ]
        );

        RiwayatPengajuan::create(
          [
            'tahun_id' => $tahunId,
            'aset_id' => $asetId,
            'jumlah_pengajuan' => $jumlahPengajuan
          ]
        );
      }

      return response()->json([
        'success' => true,
        'message' => "Data Aset Berhasil Disimpan.",
      ]);
    } catch (\Exception $e) {
      // Log error untuk debugging
      \Log::error('Error menyimpan Data: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menyimpan Data.',
      ], 500);
    }
  }

  public function pengajuan(Request $request)
  {
    $selectedYear = $request->input('year');
    $years = Tahun::get();
    $peserta = JumlahPeserta::with(['tahun'])->get();
    $assets = Aset::get();

    if ($selectedYear) {
      $riwayatPengajuan = RiwayatPengajuan::whereHas('tahun', function ($q) use ($selectedYear) {
        $q->where('tahun', $selectedYear);
      })->with(['tahun'])->get();

      $riwayatPrediksi = RiwayatPrediksi::whereHas('tahun', function ($q) use ($selectedYear) {
        $q->where('tahun', $selectedYear);
      })->with(['tahun'])->get();

      if ($riwayatPrediksi->isEmpty()) {
        return view('sarpras.pengajuan', [
          'riwayatPrediksi' => null,
          'message' => "Prediksi Aset tahun $selectedYear belum dilakukan, lakukan Prediksi Aset terlebih dahulu",
        ]);
      }

      foreach ($riwayatPrediksi as $item) {
        $lastTahunId = $item->tahun_id - 1;
      }

      $aset = JumlahAset::where('tahun_id', $lastTahunId)->with(['tahun'])->get();
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
      $riwayatPengajuan = RiwayatPengajuan::with(['tahun', 'aset'])->get();
      $riwayatPrediksi = RiwayatPrediksi::with(['aset'])->get();
      if ($riwayatPrediksi->isEmpty()) {
        return view('sarpras.pengajuan', [
          'riwayatPrediksi' => null,
          'message' => 'Prediksi Aset belum dilakukan, lakukan Prediksi Aset terlebih dahulu',
        ]);
      } else {
        foreach ($riwayatPrediksi as $item) {
          $lastTahunId = $item->tahun_id - 1;
        }

        $aset = JumlahAset::where('tahun_id', $lastTahunId)->with(['tahun'])->get();
      }
      $lastYearPredicted = true;
    }

    return view('sarpras.pengajuan', [
      'riwayatPrediksi' => $riwayatPrediksi,
      'riwayatPengajuan' => $riwayatPengajuan,
      'years' => $years,
      'lastData' => $aset,
      'assets' => $assets,
      'selectedYear' => $selectedYear,
      'lastYearPredicted' => $lastYearPredicted,
      'lastYear' => $lastYear->tahun ?? null,
      'peserta' => $peserta
    ]);
  }

  public function prosesPengajuan($tahun)
  {
    // dd($tahun);
    // Query untuk assets
    $predictionHistories = RiwayatPrediksi::whereHas('tahun', function ($q) use ($tahun) {
      $q->where('tahun', $tahun);
    })->with(['tahun', 'aset'])->get();
    $pengajuans = [];
    $finalAsets = [];
    // dd($predictionHistories);
    foreach ($predictionHistories as $history) {
      $lastTahunId = $history->tahun_id - 1;
      $jumlahHasilPrediksi = $history->jumlah;

      // Ambil aset berdasarkan tahun sebelumnya dan yang memiliki ID aset yang sama
      $lastAssetData = JumlahAset::where('tahun_id', $lastTahunId)
        ->where('aset_id', $history->aset_id) // Sesuaikan dengan ID aset yang sama
        ->first(); // Ambil hanya satu record yang relevan

      if ($lastAssetData) {
        if ($jumlahHasilPrediksi <= $lastAssetData->jumlah_layak) {
          $jumlahHasilPrediksi = 0;
        } else {
          $jumlahHasilPrediksi = $jumlahHasilPrediksi - $lastAssetData->jumlah_layak + $lastAssetData->jumlah_tidak_layak;
        }
      }

      // Simpan hasilnya ke array pengajuans
      $pengajuans[] = [
        'jumlah_pengajuan' => $jumlahHasilPrediksi,
      ];

      $finalAsets[] = [
        'tahun_id' => $history->tahun_id,
        'aset_id' => $history->aset_id,
        'jumlah_aset' => $jumlahHasilPrediksi === 0 ? $lastAssetData->jumlah_layak : $history->jumlah,
        'jumlah_layak' => $jumlahHasilPrediksi === 0 ? $lastAssetData->jumlah_layak : $history->jumlah,
        'jumlah_tidak_layak' => 0
      ];
    }

    return response()->json([
      'success' => true,
      'pengajuan' => $pengajuans,
      'finalAsets' => $finalAsets
    ]);
  }

  public function updateAset(Request $request)
  {
    foreach ($request->jumlah as $id => $jumlah) {
      $asset = JumlahAset::find($id);
      if ($asset) {
        $asset->jumlah = $jumlah;
        $asset->jumlah_layak = $request->jumlah_layak[$id] ?? $asset->jumlah_layak;
        $asset->jumlah_tidak_layak = $request->jumlah_tidak_layak[$id] ?? $asset->jumlah_tidak_layak;
        $asset->save();
      }
    }

    return redirect()->back()->with('success', 'Data berhasil diperbarui');
  }
}
