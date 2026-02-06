<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->enum('day_type', ['working', 'holiday', 'dayoff'])->default('working');
            $table->date('sched_date');
            $table->datetime('sched_start')->nullable();
            $table->datetime('sched_end')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('employee_id')
                ->references('employee_id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['employee_id', 'sched_date'], 'employee_schedule_unique');
            
            $table->index('sched_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};