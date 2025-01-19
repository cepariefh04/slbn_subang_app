<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JumlahAset extends Model
{
    use HasFactory;

    protected $fillable = ['tahun_id', 'aset_id', 'jumlah', 'jumlah_layak', 'jumlah_tidak_layak'];

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }

    public function aset(): BelongsTo
    {
        return $this->belongsTo(Aset::class);
    }
}
