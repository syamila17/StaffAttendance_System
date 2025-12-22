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
        if (!Schema::hasTable('staff_sessions')) {
            Schema::create('staff_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_id');
                $table->string('session_id')->unique();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('logged_in_at');
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();

                $table->foreign('staff_id')
                    ->references('staff_id')
                    ->on('staff')
                    ->onDelete('cascade');

                $table->index('staff_id');
                $table->index('session_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_sessions');
    }
};
