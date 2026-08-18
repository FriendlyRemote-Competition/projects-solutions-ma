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
        Schema::create('lines', function (Blueprint $table) {
            $table->string('code', 4)->primary();
            $table->string('name');
            $table->string('status')->default('active');
            $table->string('station_a_code');
            $table->string('station_b_code');
            $table->integer('seat_capacity');
            $table->integer('crossing_minutes');
            $table->decimal('fare_cny', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lines');
    }
};
