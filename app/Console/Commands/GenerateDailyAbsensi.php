<?php

namespace App\Console\Commands;

use App\Models\Apps\Absensi;
use App\Models\Apps\Kelas;
use App\Models\Apps\PeriodeAkademik;
use App\Models\Reference\StatusVerifikasi;
use App\Services\DispensasiPropagator;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDailyAbsensi extends Command
{
    protected $signature = 'absensi:generate
                            {--date= : Target date (Y-m-d). Defaults to today.}
                            {--force : Generate even if the target date is a weekend.}';

    protected $description = 'Generate an empty attendance (Absensi) record for every active class on the given date. Skips weekends and existing records.';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::now()->startOfDay();

        if ($date->isWeekend() && !$this->option('force')) {
            $this->info("Skip: {$date->toDateString()} is a weekend.");
            return self::SUCCESS;
        }

        $periodeAktif = PeriodeAkademik::where('status', 1)->first();
        if (!$periodeAktif) {
            $this->error('No active Periode Akademik found.');
            return self::FAILURE;
        }

        $statusMenungguPengisianId = StatusVerifikasi::where('nama_status', 'Menunggu Pengisian')
            ->where('status', 1)
            ->value('id');

        $kelasList = Kelas::where('status', 1)->get();
        if ($kelasList->isEmpty()) {
            $this->warn('No active classes found.');
            return self::SUCCESS;
        }

        $created     = 0;
        $skipped     = 0;
        $dispenTotal = 0;

        DB::beginTransaction();
        try {
            foreach ($kelasList as $kelas) {
                $exists = Absensi::where('kelas_id', $kelas->id)
                    ->whereDate('tanggal', $date->toDateString())
                    ->where('status', 1)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $absensi = Absensi::create([
                    'kelas_id'             => $kelas->id,
                    'tanggal'              => $date->toDateString(),
                    'status_verifikasi_id' => $statusMenungguPengisianId,
                    'periode_akademik_id'  => $periodeAktif->id,
                    'status'               => 1,
                    'user_input'           => 'system',
                    'tanggal_input'        => now(),
                ]);

                $dispenTotal += DispensasiPropagator::applyApprovedDispensasiToAbsensi($absensi, 'system');
                $created++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("Date {$date->toDateString()} — created: {$created}, skipped: {$skipped}, dispensasi details applied: {$dispenTotal}, classes: {$kelasList->count()}.");
        return self::SUCCESS;
    }
}
