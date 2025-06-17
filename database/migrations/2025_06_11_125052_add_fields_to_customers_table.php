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
        Schema::table('customers', function (Blueprint $table) {
            //
            $table->string('mat_inps')->nullable()->after('cod_univoco');
            $table->integer('n_dipendenti')->nullable()->after('mat_inps');
            $table->boolean('aderente')->default(true)->after('n_dipendenti');
            $table->string('fondo')->nullable()->after('aderente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
