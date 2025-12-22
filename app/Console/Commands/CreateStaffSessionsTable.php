<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStaffSessionsTable extends Command
{
    protected $signature = 'db:create-staff-sessions-table';
    protected $description = 'Create staff_sessions table if it does not exist';

    public function handle()
    {
        try {
            if (!Schema::hasTable('staff_sessions')) {
                DB::statement('
                    CREATE TABLE staff_sessions (
                        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        staff_id BIGINT UNSIGNED NOT NULL,
                        session_id VARCHAR(255) NOT NULL UNIQUE,
                        ip_address VARCHAR(45) NULL,
                        user_agent TEXT NULL,
                        logged_in_at TIMESTAMP NOT NULL,
                        last_activity_at TIMESTAMP NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        KEY staff_id_idx (staff_id),
                        KEY session_id_idx (session_id),
                        CONSTRAINT staff_sessions_staff_id_foreign 
                            FOREIGN KEY (staff_id) 
                            REFERENCES staff(staff_id) 
                            ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ');
                $this->info('✓ staff_sessions table created successfully!');
            } else {
                $this->info('✓ staff_sessions table already exists');
            }
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
