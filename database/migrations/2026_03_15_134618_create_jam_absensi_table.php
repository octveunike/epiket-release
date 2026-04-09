<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_absensi', function (Blueprint $table) {
            $table->id();
            $table->integer('jam_ke')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('jam_absensi');
    }
};