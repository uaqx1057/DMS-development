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

            // Relations
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade');

            $table->foreignId('business_id_id')
                ->constrained('business_ids')
                ->onDelete('cascade');

            $table->foreignId('previous_driver_id')
                ->nullable()
                ->constrained('drivers')
                ->onDelete('set null');

            // Timestamps for tracking assignment history
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('transferred_at')->nullable();

            $table->timestamps();

            // 🔹 Indexes (optimized for reports & lookups)
            $table->index(['business_id_id', 'assigned_at']);
            $table->index(['driver_id', 'transferred_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_business_ids');
    }
};
