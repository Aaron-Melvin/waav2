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
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->string('code');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('discount_type')->default('percent');
            $table->decimal('discount_value', 10, 2);
            $table->unsignedInteger('max_redemptions')->nullable();
            $table->unsignedInteger('max_per_customer')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->decimal('min_total', 10, 2)->nullable();
            $table->string('status')->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['partner_id', 'code']);
            $table->foreign('partner_id')->references('id')->on('partners');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
