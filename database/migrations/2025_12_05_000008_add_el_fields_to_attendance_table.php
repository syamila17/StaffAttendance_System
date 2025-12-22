<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Add EL (Emergency Leave) specific fields
            if (!Schema::hasColumn('attendance', 'el_reason')) {
                $table->text('el_reason')->nullable()->after('remarks')->comment('Reason for Emergency Leave');
            }
            if (!Schema::hasColumn('attendance', 'el_proof_file')) {
                $table->string('el_proof_file')->nullable()->after('el_reason')->comment('EL supporting document filename');
            }
            if (!Schema::hasColumn('attendance', 'el_proof_file_path')) {
                $table->string('el_proof_file_path')->nullable()->after('el_proof_file')->comment('EL supporting document path in storage');
            }
            if (!Schema::hasColumn('attendance', 'el_proof_uploaded_at')) {
                $table->timestamp('el_proof_uploaded_at')->nullable()->after('el_proof_file_path')->comment('When EL proof was uploaded');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn([
                'el_reason',
                'el_proof_file',
                'el_proof_file_path',
                'el_proof_uploaded_at',
            ]);
        });
    }
};
