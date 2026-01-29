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
        Schema::create('holds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('product_id')->nullable()->index();
            $table->uuid('event_id')->nullable()->index();
            $table->uuid('unit_id')->nullable()->index();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status')->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holds');
    }
};
