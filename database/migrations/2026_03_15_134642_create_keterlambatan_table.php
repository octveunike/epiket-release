<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keterlambatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_id')->nullable()->constrained('absensi')->nullOnDelete();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->nullOnDelete();
            $table->dateTime('waktu_masuk')->nullable();
            $table->string('alasan')->nullable();
            $table->foreignId('periode_akademik_id')->nullable()->constrained('periode_akademik')->nullOnDelete();
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
        Schema::dropIfExists('keterlambatan');
    }
};