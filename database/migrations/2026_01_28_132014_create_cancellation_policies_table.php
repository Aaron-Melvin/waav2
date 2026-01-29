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
        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('rules');
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellation_policies');
    }
};
