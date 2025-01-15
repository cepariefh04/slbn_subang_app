<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tahun extends Model
{
    use HasFactory;

    protected $fillable = ['tahun'];

    public function jumlahAsets(): HasMany
    {
        return $this->hasMany(JumlahAset::class);
    }

    public function jumlahPeserta(): HasMany
    {
        return $this->hasMany(JumlahPeserta::class);
    }
}
