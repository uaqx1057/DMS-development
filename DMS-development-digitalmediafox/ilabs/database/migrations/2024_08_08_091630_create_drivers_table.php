<?php

use App\Models\Branch;
use App\Models\DriverType;
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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(DriverType::class)->constrained();
            $table->text('image')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('driver_id', 255)->nullable();
            $table->string('iqaama_number', 255)->nullable();
            $table->date('iqaama_expiry')->nullable();
            $table->date('dob')->nullable();
            $table->string('absher_number', 255)->nullable();
            $table->string('sponsorship', 255)->nullable();
            $table->string('sponsorship_id', 255)->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('insurance_policy_number', 255)->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->decimal('vehicle_monthly_cost', 10, 2)->default(0);
            $table->decimal('mobile_data', 10, 2)->default(0);
            $table->decimal('fuel', 10, 2)->default(0);
            $table->decimal('gprs', 10, 2)->default(0);
            $table->decimal('government_levy_fee', 10, 2)->default(0);
            $table->decimal('accommodation', 8, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
