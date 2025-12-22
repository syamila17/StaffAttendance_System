<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create departments table
     */
    public function up(): void
    {
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id('department_id');
                $table->string('department_name')->unique();
                $table->string('department_code')->unique();
                $table->text('description')->nullable();
                $table->string('location')->nullable();
                $table->unsignedBigInteger('manager_id')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                
                // Self-referencing for sub-departments (optional)
                // $table->unsignedBigInteger('parent_department_id')->nullable();
            // $table->foreign('parent_department_id')->references('department_id')->on('departments')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
