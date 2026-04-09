<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_detail_jam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_detail_id')->nullable()->constrained('absensi_detail')->nullOnDelete();
            $table->foreignId('jam_ke_id')->nullable()->constrained('jam_absensi')->nullOnDelete();
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
        Schema::dropIfExists('absensi_detail_jam');
    }
};