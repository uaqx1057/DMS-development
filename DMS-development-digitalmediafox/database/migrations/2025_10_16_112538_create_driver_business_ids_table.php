<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_business_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('business_id_id')->constrained('business_ids')->onDelete('cascade');
            $table->foreignId('previous_driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('transferred_at')->nullable();
            $table->timestamps();
            
            // Ensure one business_id can only be assigned to one driver at a time
            $table->unique(['business_id_id', 'driver_id']);
            $table->index(['business_id_id', 'assigned_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_business_ids');
    }
};