<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); // Pengguna yang membuat laporan
            $table->uuid('program_id'); // Program bantuan
            $table->string('provinsi'); // Nama provinsi
            $table->string('kabupaten'); // Nama kabupaten
            $table->string('kecamatan'); // Nama kecamatan
            $table->integer('jumlah_penerima'); // Jumlah penerima bantuan
            $table->date('tanggal_penyaluran'); // Tanggal penyaluran bantuan
            $table->string('bukti_penyaluran'); // Path file bukti
            $table->text('catatan_tambahan')->nullable(); // Catatan tambahan
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending'); // Status laporan
            $table->text('alasan_penolakan')->nullable(); // Alasan jika laporan ditolak
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
