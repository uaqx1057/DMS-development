<?php

use App\Enums\Range;
use App\Models\Business;
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
        Schema::create('driver_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Business::class)->constrained();
            $table->enum('calculation_type', array_column(Range::cases(), 'value'))->nullable();
            $table->decimal('from', 8,2)->nullable();
            $table->decimal('to', 8,2)->nullable();
            $table->decimal('amount')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_calculations');
    }
};
