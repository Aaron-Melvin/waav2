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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('location_id')->nullable()->index();
            $table->string('name');
            $table->string('type');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedInteger('capacity_total')->nullable();
            $table->string('default_currency', 3)->nullable();
            $table->string('status')->default('active');
            $table->string('visibility')->default('public');
            $table->unsignedInteger('lead_time_minutes')->nullable();
            $table->unsignedInteger('cutoff_minutes')->nullable();
            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['partner_id', 'type', 'slug']);
            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
