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
            $table->string('out_meter_reading')->nullable()->after('car_image');
            $table->string('out_meter_image')->nullable()->after('out_meter_reading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->dropColumn(['out_meter_reading', 'out_meter_image']);
        });
    }
};
