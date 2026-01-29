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
        Schema::create('calendar_sync_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('calendar_sync_account_id')->index();
            $table->uuid('product_id')->nullable()->index();
            $table->uuid('unit_id')->nullable()->index();
            $table->uuid('event_id')->nullable()->index();
            $table->string('external_event_id')->nullable();
            $table->string('direction')->default('push');
            $table->string('status')->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('calendar_sync_account_id')->references('id')->on('calendar_sync_accounts');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_sync_events');
    }
};
