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
        Schema::create('card_payments', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('start_month');
            $table->string('boot_cycle');
            $table->string('promotion');
            $table->date('limit_date');
            $table->string('note_one')->nullable();
            $table->string('note_two')->nullable();
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
        Schema::dropIfExists('card_payments');
    }
};
