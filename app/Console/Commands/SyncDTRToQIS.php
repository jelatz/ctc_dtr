<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncDTRToQIS extends Command
{
    protected $signature = 'app:sync-dtr-to-qis';
    protected $description = 'Syncs data from new dtr to qis dtr_data table';

    public function handle(): int
    {
        $this->info('Starting DTR sync to QIS...');

        try {
            DB::table('dtr_logs')
                ->where('is_imported', 0)
                ->orderBy('id')
                ->chunk(500, function ($dtr_logs) {
                    
                    $importedIds = [];

                    foreach ($dtr_logs as $dtr_log) {
                        try {
                            $timeString = $dtr_log->created_at ? date('H:i:s', strtotime($dtr_log->created_at)) : null;

                            $updateData = [
                                'create_date' => $dtr_log->created_at,
                                'dtr_date'    => $dtr_log->dtr_date,
                            ];

                            if ($dtr_log->type === "login") {
                                $updateData['login1'] = $timeString;
                                $updateData['login_datetime'] = $dtr_log->created_at;
                                $updateData['SyncToQIS'] = 0;
                            } else {
                                $updateData['logout1'] = $timeString;
                                $updateData['logout_datetime'] = $dtr_log->created_at;
                            }

                            DB::connection('mysql_qis')->table('dtr_data')->updateOrInsert(
                                [
                                    'employee_id' => $dtr_log->employee_id,
                                    'dtr_date'    => $dtr_log->dtr_date,
                                ],
                                $updateData
                            );

                            $importedIds[] = $dtr_log->id;

                        } catch (Throwable $e) {
                            Log::error("Failed to import DTR log ID {$dtr_log->id}: " . $e->getMessage());
                        }
                    }

                    // Bulk update local logs to imported
                    if (!empty($importedIds)) {
                        DB::table('dtr_logs')->whereIn('id', $importedIds)->update(['is_imported' => 1]);
                    }
                });

            $this->info('DTR sync completed successfully.');
            return Command::SUCCESS;

        } catch (Throwable $e) {
            Log::error('Critical DTR Sync Error: ' . $e->getMessage());
            $this->error('DTR sync encountered a critical error.');
            return Command::FAILURE;
        }
    }
}