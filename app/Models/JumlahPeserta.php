<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JumlahPeserta extends Model
{
    use HasFactory;

    protected $fillable = ['laki-laki', 'perempuan', 'jumlah'];

    public function tahun(): BelongsTo
    {
        return $this->belongsTo(Tahun::class);
    }
}
