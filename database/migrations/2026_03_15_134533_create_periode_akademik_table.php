<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_akademik', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode')->nullable();
            $table->string('tahun_ajaran')->nullable();
            $table->string('semester')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
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
        Schema::dropIfExists('periode_akademik');
    }
};