<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_prediksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained()->cascadeOnDelete();
            $table->foreignId('aset_id')->constrained()->cascadeOnDelete();
            $table->integer('jumlah');
            $table->integer('jumlah_layak');
            $table->integer('jumlah_tidak_layak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_prediksis');
    }
};
