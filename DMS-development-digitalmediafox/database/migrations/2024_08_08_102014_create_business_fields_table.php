<?php

use App\Enums\FieldsTypes;
use App\Models\Business;
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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('short_name', 255)->nullable();
            $table->enum('type', array_column(FieldsTypes::cases(), 'value'))->default(FieldsTypes::TEXT);
            $table->boolean('required')->default(false);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
