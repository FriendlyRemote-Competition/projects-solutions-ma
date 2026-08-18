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
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('booking_code', 15)->primary();
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('email');
            $table->string('phone')->nullable();
            $table->integer('seats');
            $table->string('status')->default('confirmed');
            $table->decimal('fare_cny', 8, 2);
            $table->decimal('total_fare_cny', 8, 2);
            $table->string('departure_code')->index();
            $table->date('departure_date');
            $table->time('departure_time');
            $table->string('line_code');
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
