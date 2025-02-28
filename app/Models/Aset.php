<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aset extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    public function jumlahAsets(): HasMany
    {
        return $this->hasMany(JumlahAset::class);
    }
}
