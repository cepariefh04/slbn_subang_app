<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPengajuan extends Model
{
  use HasFactory;

  protected $fillable = ['tahun_id', 'aset_id', 'jumlah_pengajuan'];

  public function tahun(): BelongsTo
  {
    return $this->belongsTo(Tahun::class);
  }

  public function aset(): BelongsTo
  {
    return $this->belongsTo(Aset::class);
  }
}
