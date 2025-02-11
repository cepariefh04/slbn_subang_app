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
    Schema::create('riwayat_pengajuans', function (Blueprint $table) {
      $table->id();
      $table->foreignId('tahun_id')->constrained()->cascadeOnDelete();
      $table->foreignId('aset_id')->constrained()->cascadeOnDelete();
      $table->integer('jumlah_pengajuan');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('riwayat_pengajuans');
  }
};
