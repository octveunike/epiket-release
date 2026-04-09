<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->nullable();
            $table->string('nama_siswa')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('status_siswa_id')->nullable()->constrained('status_siswa')->nullOnDelete();
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
        Schema::dropIfExists('siswa');
    }
};