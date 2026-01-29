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
        Schema::create('inventory_ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('product_id')->nullable()->index();
            $table->uuid('event_id')->nullable()->index();
            $table->uuid('unit_id')->nullable()->index();
            $table->uuid('booking_id')->nullable()->index();
            $table->uuid('hold_id')->nullable()->index();
            $table->integer('delta');
            $table->string('reason')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('hold_id')->references('id')->on('holds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_ledgers');
    }
};
