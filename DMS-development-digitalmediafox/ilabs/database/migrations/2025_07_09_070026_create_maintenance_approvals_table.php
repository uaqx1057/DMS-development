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
       Schema::create('maintenance_approvals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('maintenance_id')->constrained('vehicle_maintenances')->onDelete('cascade');
    $table->decimal('estimated_cost', 10, 2);
    $table->date('scheduled_date');
    $table->text('approval_notes');
    $table->string('approved_by')->nullable(); // Optional: store user name
    $table->timestamps();
});
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_approvals');
    }
};
