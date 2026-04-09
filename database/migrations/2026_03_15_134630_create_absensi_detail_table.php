<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_id')->nullable()->constrained('absensi')->nullOnDelete();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->nullOnDelete();
            $table->boolean('is_full_day')->nullable();
            $table->foreignId('status_absensi_id')->nullable()->constrained('status_absensi')->nullOnDelete();
            $table->string('keterangan')->nullable();
            $table->string('lampiran_absensi')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('user_input', 100)->nullable();
            $table->dateTime('tanggal_input')->nullable();
            $table->string('user_update', 100)->nullable();
            $table->dateTime('tanggal_update')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_detail');
    }
};