<?php

use App\Models\Branch;
use App\Models\CoordinatorReport;
use App\Models\Driver;
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
        Schema::table('coordinator_reports', function (Blueprint $table) {
            // First, add as nullable
            $table->foreignId('branch_id')->nullable()->constrained()->after('driver_id');
        });

        // Update existing records with driver's branch
        CoordinatorReport::with('driver.branch')->chunk(100, function ($reports) {
            foreach ($reports as $report) {
                if ($report->driver && $report->driver->branch) {
                    $report->branch_id = $report->driver->branch->id;
                    $report->save();
                }
            }
        });

        // Make it required after populating data
        Schema::table('coordinator_reports', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coordinator_reports', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};