<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertStaffIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:convert-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert numeric staff_ids to formatted ST##### format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $this->info('Starting staff_id conversion...');

            // Disable foreign key checks to avoid constraint issues
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $this->info('✓ Foreign key checks disabled');

            // Get all staff before we modify the table
            $this->info('Fetching staff records...');
            $staffList = DB::table('staff')->get();
            $this->info("Found {$staffList->count()} staff members");

            if ($staffList->count() === 0) {
                $this->warn('No staff records found');
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                return 0;
            }

            // Step 1: Modify column type to VARCHAR
            $this->info('Converting staff_id column to VARCHAR...');
            DB::statement('ALTER TABLE staff MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');
            $this->info('✓ Column type changed to VARCHAR(20)');

            // Step 2: Convert each staff_id
            $this->info('Converting staff IDs...');
            $counter = 10;
            $mapping = []; // Track old → new mapping

            foreach ($staffList as $staff) {
                $oldId = $staff->staff_id;
                $newId = 'ST1101' . str_pad($counter++, 2, '0', STR_PAD_LEFT);
                
                // Update staff table
                DB::update('UPDATE staff SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
                $mapping[$oldId] = $newId;
                
                $this->line("  {$staff->staff_name}: {$oldId} → {$newId}");
            }

            // Step 3: Update references in other tables
            $this->info('Updating related tables...');
            
            foreach ($mapping as $oldId => $newId) {
                // Update staff_profile
                DB::update('UPDATE staff_profile SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
                
                // Update attendance
                DB::update('UPDATE attendance SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
                
                // Update attendance_report
                DB::update('UPDATE attendance_report SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
            }
            $this->info('✓ Related tables updated');

            // Step 4: Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('✓ Foreign key checks re-enabled');

            // Step 5: Verify
            $this->info("\n=== Verification ===");
            $converted = DB::table('staff')->select('staff_id', 'staff_name')->orderBy('staff_id')->get();
            
            foreach ($converted as $c) {
                $this->line("  {$c->staff_id}: {$c->staff_name}");
            }

            DB::commit();
            $this->info("\n✓✓✓ Conversion completed successfully!");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error("\n✗ Error: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
