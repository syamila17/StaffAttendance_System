<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create attendance_reports table for storing generated reports
     */
    public function up(): void
    {
        Schema::create('attendance_reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('report_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('report_type', ['department', 'team', 'staff', 'summary'])->default('summary');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->integer('total_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('cascade');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('set null');
            $table->foreign('team_id')->references('team_id')->on('teams')->onDelete('set null');
            $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('set null');
            
            // Indexes for faster queries
            $table->index('report_type');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_reports');
    }
};
