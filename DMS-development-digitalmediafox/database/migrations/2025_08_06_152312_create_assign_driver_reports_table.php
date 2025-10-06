<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignDriverReportsTable extends Migration
{
    public function up()
    {
        Schema::create('assign_driver_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->date('add_date')->nullable();
            $table->enum('status', ['assign', 'unassign']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assign_driver_reports');
    }
}
