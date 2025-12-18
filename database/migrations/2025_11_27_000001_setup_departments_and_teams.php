<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert departments if they don't exist
        $dda = DB::table('departments')->where('department_code', 'DDA')->first();
        if (!$dda) {
            DB::table('departments')->insert([
                'department_name' => 'DDA',
                'department_code' => 'DDA',
                'description' => 'Data & Development Administration',
                'location' => 'Main Office',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $ddi = DB::table('departments')->where('department_code', 'DDI')->first();
        if (!$ddi) {
            DB::table('departments')->insert([
                'department_name' => 'DDI',
                'department_code' => 'DDI',
                'description' => 'Digital Development & Innovation',
                'location' => 'Main Office',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Get department IDs
        $ddaId = DB::table('departments')->where('department_code', 'DDA')->first()->department_id;
        $ddiId = DB::table('departments')->where('department_code', 'DDI')->first()->department_id;

        // Insert teams for DDA if they don't exist
        $this->insertTeamIfNotExists('Network', 'NETWORK', $ddaId);
        $this->insertTeamIfNotExists('Data Centre', 'DATACENTER', $ddaId);

        // Insert teams for DDI if they don't exist
        $this->insertTeamIfNotExists('Research', 'RESEARCH', $ddiId);
        $this->insertTeamIfNotExists('Operation', 'OPERATION', $ddiId);
        $this->insertTeamIfNotExists('Human Resources', 'HR', $ddiId);
    }

    private function insertTeamIfNotExists($name, $code, $departmentId)
    {
        $exists = DB::table('teams')->where('team_code', $code)->first();
        if (!$exists) {
            DB::table('teams')->insert([
                'team_name' => $name,
                'team_code' => $code,
                'department_id' => $departmentId,
                'description' => $name . ' Team',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down(): void
    {
        // Keep data on rollback
    }
};
