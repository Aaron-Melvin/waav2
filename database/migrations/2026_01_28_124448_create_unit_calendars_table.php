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
        Schema::create('unit_calendars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('unit_id')->index();
            $table->date('date');
            $table->boolean('is_available')->default(true);
            $table->unsignedInteger('min_stay_nights')->nullable();
            $table->unsignedInteger('max_stay_nights')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['unit_id', 'date']);
            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_calendars');
    }
};
