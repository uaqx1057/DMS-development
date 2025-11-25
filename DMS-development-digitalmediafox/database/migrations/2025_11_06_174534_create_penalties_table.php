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
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id_value')->nullable()->constrained('business_ids')->nullOnDelete();
            $table->foreignId('coordinator_report_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('penalty_date');
            $table->integer('penalty_value');
            $table->string('penalty_file')->nullable();
            $table->integer('is_from_coordinate')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
