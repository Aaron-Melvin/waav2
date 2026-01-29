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
        Schema::create('booking_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id')->index();
            $table->uuid('event_id')->nullable()->index();
            $table->uuid('unit_id')->nullable()->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_allocations');
    }
};
