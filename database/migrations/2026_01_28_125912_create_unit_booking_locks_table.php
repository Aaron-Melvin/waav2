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
        Schema::create('unit_booking_locks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id')->index();
            $table->uuid('unit_id')->index();
            $table->date('date');
            $table->timestamps();

            $table->unique(['unit_id', 'date']);
            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_booking_locks');
    }
};
