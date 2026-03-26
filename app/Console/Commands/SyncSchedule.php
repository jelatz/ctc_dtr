<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class SyncSchedule extends Command
{
    protected $signature = 'app:sync-schedule';
    protected $description = 'Sync schedules from QIS database to DTR';

    public function handle(): int
    {
        $this->info('Starting schedule sync...');

        $startDate = now()->subMonth()->toDateString();
        $endDate   = now()->addWeek()->toDateString();

        try {
            DB::connection('mysql_qis')
                ->table('Schedules as qs')
                ->join('Users as qu', 'qu.UserID', '=', 'qs.UserID')
                ->select([
                    'qu.EmployeeID',
                    'qs.SchedDate',
                    'qs.TimeIn',
                    'qs.TimeOut',
                    'qs.SchedStatus',
                ])
                ->where('qu.Hide', 0)
                ->whereBetween('qs.SchedDate', [$startDate, $endDate])
                ->orderBy('qs.SchedDate')
                ->orderBy('qu.EmployeeID')
                ->chunk(500, function ($rows) {
                    // Get all EmployeeIDs from this chunk
                    $employeeIds = $rows->pluck('EmployeeID')->unique()->toArray();

                    // Verify which of these IDs actually exist in our local 'users' table
                    $existingEmployeeIds = DB::table('users')
                        ->whereIn('employee_id', $employeeIds)
                        ->pluck('employee_id')
                        ->toArray();

                    $upsertData = [];

                    foreach ($rows as $row) {
                        // Skip if the employee does not exist locally to avoid FK violation
                        if (!in_array($row->EmployeeID, $existingEmployeeIds)) {
                            continue;
                        }

                        $upsertData[] = [
                            'employee_id' => $row->EmployeeID,
                            'sched_date'  => Carbon::parse($row->SchedDate)->toDateString(),
                            'day_type'    => $row->SchedStatus == 0 ? 'working' : 'dayoff',
                            'sched_start' => $row->TimeIn ? Carbon::parse($row->TimeIn) : null,
                            'sched_end'   => $row->TimeOut ? Carbon::parse($row->TimeOut) : null,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ];
                    }

                    if (!empty($upsertData)) {
                        DB::table('schedules')->upsert(
                            $upsertData,
                            ['employee_id', 'sched_date'],
                            ['day_type', 'sched_start', 'sched_end', 'updated_at']
                        );
                    }
                });

            $this->info('Schedule sync completed successfully.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            Log::error('Schedule Sync Failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            $this->error('Schedule sync encountered an error. Check Laravel logs for details.');

            return self::FAILURE;
        }
    }
}
