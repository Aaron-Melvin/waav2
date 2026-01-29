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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('product_id')->index();
            $table->uuid('event_series_id')->nullable()->index();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedInteger('capacity_total')->nullable();
            $table->unsignedInteger('capacity_reserved')->default(0);
            $table->string('traffic_light')->nullable();
            $table->string('status')->default('scheduled');
            $table->string('publish_state')->default('draft');
            $table->boolean('weather_alert')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('event_series_id')->references('id')->on('event_series');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
