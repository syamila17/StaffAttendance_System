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
        Schema::table('leave_requests', function (Blueprint $table) {
            // Add columns for proof file uploads
            $table->string('proof_file')->nullable()->comment('Proof document filename (sick leave mandatory, emergency optional)');
            $table->string('proof_file_path')->nullable()->comment('Full path to stored proof file');
            $table->timestamp('proof_uploaded_at')->nullable()->comment('When the proof was uploaded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['proof_file', 'proof_file_path', 'proof_uploaded_at']);
        });
    }
};
