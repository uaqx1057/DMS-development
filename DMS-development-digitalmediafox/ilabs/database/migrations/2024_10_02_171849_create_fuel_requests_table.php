<?php

use App\Enums\FuelRequestStatus;
use App\Models\Driver;
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
        Schema::create('fuel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Driver::class);
            $table->integer('total_orders')->default(0);  // Total orders
            $table->json('files')->nullable();
            $table->enum('status', array_column(FuelRequestStatus::cases(), 'value'))->default(FuelRequestStatus::Pending->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_requests');
    }
};
