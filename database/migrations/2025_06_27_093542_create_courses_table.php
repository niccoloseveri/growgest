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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('pianos_id')->nullable();
            $table->foreignId('strumentos_id')->nullable();
            $table->integer('partecipanti_previsti')->default(0)->nullable();
            $table->integer('ore_totali')->nullable();
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->date('scadenza')->nullable();
            $table->string('stato')->default('in_programma')->nullable(); // e.g., 'in_programma', 'in_corso', 'concluso'
            $table->integer('valore_corso')->nullable(); // Valore del corso in euro
            $table->integer('budget_lordo_cfe')->nullable(); // Budget lordo CFE
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
