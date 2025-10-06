<?php

use App\Enums\OrderStatus;
use App\Models\{Business, Driver};
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Driver::class);
            $table->foreignIdFor(Business::class);
            $table->string('order_id')->uniqe();
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('delivered_time')->nullable();
            $table->timestamp('cancelled_time')->nullable();
            $table->timestamp('drop_time')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->enum('status', array_column(OrderStatus::cases(), 'value'))->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
