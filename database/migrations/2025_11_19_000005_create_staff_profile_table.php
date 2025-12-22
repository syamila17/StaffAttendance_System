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
        if (!Schema::hasTable('staff_profile')) {
            Schema::create('staff_profile', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_id')->unique();
                $table->string('full_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone_number')->nullable();
                $table->text('address')->nullable();
                $table->string('position')->nullable();
                $table->string('department')->nullable();
                $table->string('profile_image')->nullable();
                $table->timestamps();
                
                $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_profile');
    }
};
