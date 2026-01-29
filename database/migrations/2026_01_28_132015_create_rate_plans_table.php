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
        Schema::create('rate_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('product_id')->index();
            $table->uuid('cancellation_policy_id')->nullable()->index();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('pricing_model')->default('per_night');
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['partner_id', 'product_id', 'code']);
            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('cancellation_policy_id')->references('id')->on('cancellation_policies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_plans');
    }
};
