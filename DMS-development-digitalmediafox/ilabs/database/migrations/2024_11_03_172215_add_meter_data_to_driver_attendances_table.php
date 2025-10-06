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
        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->integer('meter_reading')->nullable();
            $table->text('meter_image')->nullable();
            $table->text('car_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->dropColumn(['meter_reading', 'meter_image', 'car_image']);
        });
    }
};
