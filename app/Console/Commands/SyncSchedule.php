<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncSchedule extends Command
{
    protected $signature = 'app:sync-schedule';
    protected $description = 'Sync schedules from QIS database to DTR';



    public function handle(): int
    {
        $this->info('Starting schedule sync...');
        $startDate = now()->subMonth()->toDateString();
        $endDate   = now()->addDays(5)->toDateString();



        $query = DB::connection('mysql_qis')
            ->table('Schedules as qs')
            ->join('Users as qu', 'qu.UserID', '=', 'qs.UserID')
            ->select([
                'qu.EmployeeID',
                'qs.SchedDate',
                'qs.TimeIn',
                'qs.TimeOut',
                'qs.SchedStatus',
                'qs.RowTimestamp',
            ])
            ->where('qu.Hide', 0)
            ->where('qs.Deleted', 0)
            ->whereBetween('qs.SchedDate', [$startDate, $endDate]);

        if (cache()->has('last_schedule_sync')) {
            $query->where('qs.RowTimestamp', '>', cache('last_schedule_sync'));
        }

        $query->orderBy('qs.SchedDate')
            ->chunk(500, function ($rows) {

                foreach ($rows as $row) {
                    DB::table('schedules')->updateOrInsert(
                        [
                            'employee_id' => $row->EmployeeID,
                            'sched_date'  => Carbon::parse($row->SchedDate)->toDateString(),
                        ],
                        [
                            'day_type'    => $row->SchedStatus ? 'working' : 'dayoff',
                            'sched_start' => $row->TimeIn ? Carbon::parse($row->TimeIn) : null,
                            'sched_end'   => $row->TimeOut ? Carbon::parse($row->TimeOut) : null,
                            'updated_at'  => now(),
                            'created_at'  => now(),
                        ]
                    );
                }
            });


        $this->info('Schedule sync completed.');

        return Command::SUCCESS;
    }
}
