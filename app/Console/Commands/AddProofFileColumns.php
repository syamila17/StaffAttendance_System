<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddProofFileColumns extends Command
{
    protected $signature = 'db:add-proof-columns';
    protected $description = 'Add proof_file, proof_file_path, and proof_uploaded_at columns to leave_requests table';

    public function handle()
    {
        try {
            if (!Schema::hasColumn('leave_requests', 'proof_file')) {
                DB::statement('ALTER TABLE leave_requests ADD COLUMN proof_file VARCHAR(255) NULL');
                $this->info('✓ Added proof_file column');
            }

            if (!Schema::hasColumn('leave_requests', 'proof_file_path')) {
                DB::statement('ALTER TABLE leave_requests ADD COLUMN proof_file_path VARCHAR(255) NULL');
                $this->info('✓ Added proof_file_path column');
            }

            if (!Schema::hasColumn('leave_requests', 'proof_uploaded_at')) {
                DB::statement('ALTER TABLE leave_requests ADD COLUMN proof_uploaded_at TIMESTAMP NULL');
                $this->info('✓ Added proof_uploaded_at column');
            }

            $this->info('✓ All proof file columns added successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
