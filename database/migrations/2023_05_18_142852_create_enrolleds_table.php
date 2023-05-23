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
        Schema::create('enrolleds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('boot_cycle');
            $table->string('promotion');
            $table->string('start_month');
            $table->float('total');

            $table->unsignedBigInteger('lead_id')->unique();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolleds');
    }
};
