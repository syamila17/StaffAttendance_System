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
        if (!Schema::hasTable('attendance')) {
            Schema::create('attendance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_id');
                $table->date('attendance_date');
                $table->time('check_in_time')->nullable();
                $table->time('check_out_time')->nullable();
                $table->enum('status', ['present', 'absent', 'late', 'leave'])->default('absent');
                $table->text('remarks')->nullable();
                $table->timestamps();
                
                $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
                $table->unique(['staff_id', 'attendance_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
