<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('kelas_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        // One-off backfill: link siswa rows to users by matching nama (users.nama = siswa.nama_siswa).
        // Future siswa should be linked explicitly via the admin UI.
        DB::statement('
            UPDATE siswa s
            INNER JOIN users u ON u.nama = s.nama_siswa
            SET s.user_id = u.id
            WHERE s.user_id IS NULL
              AND s.status = 1
              AND u.status = 1
        ');
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
