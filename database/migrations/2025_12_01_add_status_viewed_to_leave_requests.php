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
            if (!Schema::hasColumn('leave_requests', 'status_viewed')) {
                $table->boolean('status_viewed')->default(false)->after('rejected_at');
            }
            if (!Schema::hasColumn('leave_requests', 'status_viewed_at')) {
                $table->timestamp('status_viewed_at')->nullable()->after('status_viewed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('status_viewed');
            $table->dropColumn('status_viewed_at');
        });
    }
};
