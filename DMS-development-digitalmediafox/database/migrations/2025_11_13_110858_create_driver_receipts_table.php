<?php

use App\Models\Booklet;
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
        Schema::create('driver_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Booklet::class);
            $table->unsignedBigInteger('user_id');
            $table->string('reaceipt_no');
            $table->date('receipt_date');
            $table->string('receipt_image')->nullable();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id_value')->nullable()->constrained('business_ids')->nullOnDelete();
            $table->integer('amount_received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_receipts');
    }
};
