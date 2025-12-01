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
        Schema::table('admin', function (Blueprint $table) {
            $table->boolean('leave_notifications_viewed')->default(false)->after('admin_password');
            $table->timestamp('leave_notifications_viewed_at')->nullable()->after('leave_notifications_viewed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->dropColumn('leave_notifications_viewed');
            $table->dropColumn('leave_notifications_viewed_at');
        });
    }
};
