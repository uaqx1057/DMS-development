<?php

use App\Models\Business;
use App\Models\Field;
use App\Models\CoordinatorReport;
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
        Schema::create('coordinator_report_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CoordinatorReport::class);
            $table->foreignIdFor(Business::class);
            $table->foreignIdFor(Field::class);
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinator_report_field_values');
    }
};
