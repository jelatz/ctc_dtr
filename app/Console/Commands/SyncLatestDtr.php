<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncLatestDtr extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:sync-latest-dtr';

    /**
     * The console command description.
     */
    protected $description = 'Sync latest DTR data from QIS database to DTR';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting DTR sync...");

        // Range: Last 5 days to today
        $startDate = now()->subDays(5)->toDateString();
        $endDate = now()->toDateString();

        try {
            DB::connection('mysql_qis')
                ->table('dtr_data as dd')
                ->select('dd.employee_id', 'dd.dtr_date', 'dd.login_datetime', 'dd.logout_datetime')
                ->whereBetween('dd.dtr_date', [$startDate, $endDate])
                ->orderBy('dd.dtr_date')
                ->orderBy('dd.employee_id')
                ->chunk(500, function ($rows) {
                    // Extract IDs in this chunk to verify against local users
                    $chunkEmployeeIds = $rows->pluck('employee_id')->unique()->toArray();

                    $existingEmployeeIds = DB::table('users')
                        ->whereIn('employee_id', $chunkEmployeeIds)
                        ->pluck('employee_id')
                        ->toArray();

                    foreach ($rows as $row) {
                        // Skip if employee doesn't exist locally
                        if (!in_array($row->employee_id, $existingEmployeeIds)) {
                            continue;
                        }

                        // Sanitize zero-dates to null
                        $qisTimeIn = ($row->login_datetime === '0000-00-00 00:00:00' || empty($row->login_datetime)) 
                            ? null 
                            : $row->login_datetime;
                            
                        $qisTimeOut = ($row->logout_datetime === '0000-00-00 00:00:00' || empty($row->logout_datetime)) 
                            ? null 
                            : $row->logout_datetime;

                        // Prepare update array - Only include non-null values to avoid overwriting local data
                        $updateValues = [];
                        if ($qisTimeIn) {
                            $updateValues['time_in'] = $qisTimeIn;
                        }
                        if ($qisTimeOut) {
                            $updateValues['time_out'] = $qisTimeOut;
                        }

                        // Only proceed if there is actually data to sync
                        if (!empty($updateValues)) {
                            // updateOrInsert ensures we merge with local logs (like local logouts)
                            DB::table('dtr')->updateOrInsert(
                                [
                                    'employee_id' => $row->employee_id,
                                    'dtr_date'    => $row->dtr_date,
                                ],
                                array_merge($updateValues, [
                                    'updated_at' => now()
                                ])
                            );
                        }
                    }
                });

            $this->info('DTR sync completed successfully.');
            return Command::SUCCESS;

        } catch (Throwable $e) {
            Log::error('DTR Sync Failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            $this->error('DTR sync encountered an error. Check Laravel logs for details.');
            return Command::FAILURE;
        }
    }
}