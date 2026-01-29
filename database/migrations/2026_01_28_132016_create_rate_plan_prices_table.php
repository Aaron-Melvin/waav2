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
        Schema::create('rate_plan_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rate_plan_id')->index();
            $table->date('starts_on')->index();
            $table->date('ends_on')->index();
            $table->decimal('price', 10, 2);
            $table->decimal('extra_adult', 10, 2)->nullable();
            $table->decimal('extra_child', 10, 2)->nullable();
            $table->json('restrictions')->nullable();
            $table->timestamps();

            $table->foreign('rate_plan_id')->references('id')->on('rate_plans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_plan_prices');
    }
};
