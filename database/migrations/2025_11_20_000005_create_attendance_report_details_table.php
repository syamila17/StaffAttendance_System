<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create attendance_report_details table for storing individual attendance records in reports
     */
    public function up(): void
    {
        if (!Schema::hasTable('attendance_report_details')) {
            Schema::create('attendance_report_details', function (Blueprint $table) {
                $table->id('detail_id');
                $table->unsignedBigInteger('report_id');
                $table->unsignedBigInteger('staff_id');
                $table->unsignedBigInteger('attendance_id')->nullable();
                $table->date('attendance_date');
                $table->time('check_in_time')->nullable();
                $table->time('check_out_time')->nullable();
                $table->enum('status', ['present', 'absent', 'late', 'leave'])->default('absent');
                $table->integer('duration_minutes')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
                
                // Foreign keys
                $table->foreign('report_id')->references('report_id')->on('attendance_reports')->onDelete('cascade');
                $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
                $table->foreign('attendance_id')->references('id')->on('attendance')->onDelete('set null');
                
                // Indexes
                $table->index('report_id');
                $table->index('staff_id');
                $table->index('attendance_date');
                $table->index(['report_id', 'staff_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_report_details');
    }
};
