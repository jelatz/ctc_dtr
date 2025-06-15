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
        Schema::create('dtr', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->datetime('time_in')->nullable();
            $table->datetime('time_out')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('dtr');
        // Schema::table('dtr', function (Blueprint $table) {
        //     $table->dropForeign(['user_id']);
        // });
    }
};
