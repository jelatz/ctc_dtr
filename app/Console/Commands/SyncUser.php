<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUser extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:sync-user';

    /**
     * The console command description.
     */
    protected $description = 'Sync users from external database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting user sync...');

        // Fetch users from QIS
        DB::connection('mysql_qis')
            ->table('Users')
            ->where('Hide', 0)
            ->whereNotNull('EmployeeID')
            ->where('EmployeeID', '<>', '')
            ->where('EmployeeID', '!=', '9999')
            ->where('EmployeeID', '!=', '0')
            ->orderBy('UserID')
            ->chunk(500, function ($users) {

                foreach ($users as $user) {
                    $cleanId = trim($user->EmployeeID);

                    if (empty($cleanId)) continue;
                    DB::table('users')->updateOrInsert(
                        ['employee_id' => $cleanId],
                        [
                            'name'       => $user->Name,
                            'employee_id'      => $user->EmployeeID,
                            'updated_at' => now(),
                        ]
                    );
                }
            });

        $this->info('User sync completed.');

        return Command::SUCCESS;
    }
}
