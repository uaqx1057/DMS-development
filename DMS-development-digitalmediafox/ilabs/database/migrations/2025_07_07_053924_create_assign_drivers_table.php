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
    Schema::create('assign_drivers', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('vehicle_id');
        $table->unsignedBigInteger('driver_id');
        $table->date('assign_date');
        $table->timestamps();

        // Optional: Add foreign key constraints if needed
        $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_drivers');
    }
};
