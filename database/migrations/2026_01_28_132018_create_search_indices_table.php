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
        Schema::create('search_index', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('product_id')->nullable()->index();
            $table->uuid('event_id')->nullable()->index();
            $table->uuid('unit_id')->nullable()->index();
            $table->uuid('location_id')->nullable()->index();
            $table->date('starts_on')->nullable()->index();
            $table->date('ends_on')->nullable()->index();
            $table->integer('capacity_total')->default(0);
            $table->integer('capacity_available')->default(0);
            $table->decimal('price_min', 10, 2)->nullable();
            $table->decimal('price_max', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_index');
    }
};
