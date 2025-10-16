<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coordinator_report_field_values', function (Blueprint $table) {
            $table->foreignId('business_id_value')->nullable()->after('business_id')
                  ->comment('The specific business ID from business_ids table');
            
            // Add foreign key constraint if needed
            $table->foreign('business_id_value')->references('id')->on('business_ids')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('coordinator_report_field_values', function (Blueprint $table) {
            $table->dropForeign(['business_id_value']);
            $table->dropColumn('business_id_value');
        });
    }
};