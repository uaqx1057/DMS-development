<?php

use App\Enums\CoordinatorReportStatus;
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
        Schema::create('coordinator_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Driver::class);
            $table->date('report_date');
            $table->enum('status', array_column(CoordinatorReportStatus::cases(), 'value'))->default(CoordinatorReportStatus::Pending);
            $table->text('wallet')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinator_reports');
    }
};
