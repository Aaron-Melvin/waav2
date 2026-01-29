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
        Schema::create('eligibility_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('product_id')->nullable()->index();
            $table->string('name');
            $table->string('kind');
            $table->json('config');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligibility_rules');
    }
};
