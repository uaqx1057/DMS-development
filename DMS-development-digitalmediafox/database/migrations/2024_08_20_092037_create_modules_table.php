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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('data_key', 255)->nullable();
            $table->string('data_id', 255)->nullable();
            $table->string('route', 255)->nullable();
            $table->integer('is_view')->default(1);
            $table->integer('is_add')->default(0);
            $table->integer('is_edit')->default(0);
            $table->integer('is_delete')->default(0);
            $table->integer('type')->comment('1=Module, 2=SubModule')->default(1);
            $table->boolean('is_collapseable')->default(false);
            $table->integer('index')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('modules')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
