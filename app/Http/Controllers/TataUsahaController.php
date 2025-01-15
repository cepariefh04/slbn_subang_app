<?php

namespace App\Http\Controllers;

use App\Models\JumlahAset;
use App\Models\JumlahPeserta;
use App\Models\Tahun;
use Illuminate\Http\Request;

class TataUsahaController extends Controller
{
    public function index()
    {
        $peserta = JumlahPeserta::with(['tahun'])->get();

        return view('tata-usaha.index', [
            'peserta' => $peserta,
        ]);
    }
}
