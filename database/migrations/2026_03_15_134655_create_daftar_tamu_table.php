<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_tamu', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_kunjungan')->nullable();
            $table->string('nama')->nullable();
            $table->string('lembaga_organisasi')->nullable();
            $table->text('alamat')->nullable();
            $table->string('orang_yang_dituju')->nullable();
            $table->text('tujuan_kunjungan')->nullable();
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
        Schema::dropIfExists('daftar_tamu');
    }
};