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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('campaing')->nullable();
            $table->string('college_degree');
            $table->string('status')->default('Nuevo');
            $table->longText('other_fields')->nullable();
            $table->string('from'); //Llegada de formularios de facebook o de pagina web o ingresado manualmente.
            $table->string('boot_cycle')->nullable();
            $table->integer('count')->default(1);
            $table->boolean('newagain')->default(false);
            $table->boolean('has_card')->default(false);
            $table->boolean('enrolled')->default(false);
            $table->string('accepted_time')->nullable();

            /* Users One to Many Relationship */
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
