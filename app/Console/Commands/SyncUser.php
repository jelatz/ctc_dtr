<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncUser extends Command
{
    protected $signature = 'app:sync-user';
    protected $description = 'Sync users from external database';

    public function handle(): int
    {
        $this->info('Starting user sync...');

        try {
            DB::connection('mysql_qis')
                ->table('Users')
                ->where('Hide', 0)
                ->whereNotNull('EmployeeID')
                ->where('EmployeeID', '<>', '')
                ->whereNotIn('EmployeeID', ['0', '9999'])
                ->orderBy('UserID')
                ->chunk(500, function ($users) {
                    
                    $upsertData = [];

                    foreach ($users as $user) {
                        $cleanId = trim($user->EmployeeID);
                        if (empty($cleanId)) continue;

                        $upsertData[] = [
                            'employee_id' => $cleanId,
                            'name'        => $user->Name,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ];
                    }

                    if (!empty($upsertData)) {
                        DB::table('users')->upsert(
                            $upsertData,
                            ['employee_id'], // Unique key
                            ['name', 'updated_at'] // Columns to update
                        );
                    }
                });

            $this->info('User sync completed successfully.');
            return Command::SUCCESS;

        } catch (Throwable $e) {
            Log::error('User Sync Failed: ' . $e->getMessage());
            $this->error('User sync encountered an error.');
            return Command::FAILURE;
        }
    }
}