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
        Schema::create('vehicle_maintenances_report', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('maintenance_type')->nullable();
            $table->text('description')->nullable();
            $table->string('urgency')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('request_by')->nullable();
            $table->timestamps();

            // Optional: Foreign keys
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('request_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenances_report');
    }
};
