<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncDtrToQisJob implements ShouldQueue
{
    use Queueable;

    private string $employeeID;
    private string $dtrDate;
    private string $type;
    private string $actualDatetime;
    private Carbon $now;

    /**
     * Create a new job instance.
     */
    public function __construct(string $employeeID, string $dtrDate, string $type, string $actualDatetime, Carbon $now)
    {
        $this->employeeID = $employeeID;
        $this->dtrDate = $dtrDate;
        $this->type = $type;
        $this->actualDatetime = $actualDatetime;
        $this->now = $now;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $timeOnly = Carbon::parse($this->actualDatetime)->format('H:i:s'); // Exact time used locally

        try {
            DB::transaction(function () use ($timeOnly) {
                $qisUser = DB::connection('mysql_qis')
                    ->table('Users')
                    ->where('EmployeeID', $this->employeeID)
                    ->where('Hide', 0)
                    ->first();

                if (!$qisUser) {
                    throw new \Exception("QIS User not found for employee: {$this->employeeID}");
                }

                // Check if the record already exists
                $dtrExists = DB::connection('mysql_qis')->table('dtr_data')
                    ->where('employee_id', $this->employeeID)
                    ->where('dtr_date', $this->dtrDate)
                    ->exists();

                if ($this->type === 'login') {
                    if (!$dtrExists) {
                        DB::connection('mysql_qis')->table('dtr_data')->insert([
                            'employee_id'     => $this->employeeID,
                            'dtr_date'        => $this->dtrDate,
                            'create_date'     => $this->now,
                            'login1'          => $timeOnly,
                            'login_datetime'  => $this->actualDatetime,
                            'logout1'         => '00:00:00',
                            'logout_datetime' => '0000-00-00 00:00:00', // Legacy default
                            'SyncToQIS'       => 0,
                            'UpdatedOn'       => $this->now,
                        ]);
                    } else {
                        DB::connection('mysql_qis')->table('dtr_data')
                            ->where('employee_id', $this->employeeID)
                            ->where('dtr_date', $this->dtrDate)
                            ->update([
                                'login1'         => $timeOnly,
                                'login_datetime' => $this->actualDatetime,
                                'SyncToQIS'      => 0,
                                'UpdatedOn'      => $this->now,
                            ]);
                    }

                    DB::connection('mysql_qis')->table('AttendanceHistoryLogs')->insert([
                        'LogDate'     => $this->dtrDate,
                        'UserID'      => $qisUser->UserID,
                        'TimeIn'      => $timeOnly,
                        'TimeOut'     => '00:00:00',
                        'DateCreated' => $this->now,
                        'IPAddress'   => request()->ip() ?? '127.0.0.1', // Background job might not have request scope
                        'Source'      => 0,
                        'Deleted'     => 0,
                    ]);

                } else {
                    if (!$dtrExists) {
                        DB::connection('mysql_qis')->table('dtr_data')->insert([
                            'employee_id'     => $this->employeeID,
                            'dtr_date'        => $this->dtrDate,
                            'create_date'     => $this->now,
                            'login1'          => '00:00:00',
                            'login_datetime'  => '0000-00-00 00:00:00', // Legacy default
                            'logout1'         => $timeOnly,
                            'logout_datetime' => $this->actualDatetime,
                            'SyncToQIS'       => 0,
                            'UpdatedOn'       => $this->now,
                        ]);
                    } else {
                        DB::connection('mysql_qis')->table('dtr_data')
                            ->where('employee_id', $this->employeeID)
                            ->where('dtr_date', $this->dtrDate)
                            ->update([
                                'logout1'         => $timeOnly,
                                'logout_datetime' => $this->actualDatetime,
                                'SyncToQIS'       => 0,
                                'UpdatedOn'       => $this->now,
                            ]);
                    }

                    DB::connection('mysql_qis')->table('AttendanceHistoryLogs')->insert([
                        'LogDate'     => $this->dtrDate,
                        'UserID'      => $qisUser->UserID,
                        'TimeIn'      => '00:00:00',
                        'TimeOut'     => $timeOnly,
                        'DateCreated' => $this->now,
                        'UpdatedOn'   => $this->now,
                        'IPAddress'   => request()->ip() ?? '127.0.0.1', // Background job might not have request scope
                        'Source'      => 0,
                        'Deleted'     => 0,
                    ]);
                }

                // Mark as imported local
                DB::table('dtr_logs')
                    ->where('employee_id', $this->employeeID)
                    ->where('dtr_date', $this->dtrDate)
                    ->where('type', $this->type)
                    ->update(['is_imported' => 1]);
            });

            $duration = round(microtime(true) - $startTime, 2);
            Log::info('DTR synced to QIS successfully via Job', [
                'employee_id'      => $this->employeeID,
                'dtr_date'         => $this->dtrDate,
                'type'             => $this->type,
                'duration_seconds' => $duration,
            ]);
        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);
            Log::error('Failed to sync DTR to QIS via Job', [
                'employee_id'      => $this->employeeID,
                'dtr_date'         => $this->dtrDate,
                'type'             => $this->type,
                'duration_seconds' => $duration,
                'error'            => $e->getMessage(),
                'trace'            => $e->getTraceAsString(),
            ]);
        }
    }
}
