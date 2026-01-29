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
        Schema::create('customer_access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('customer_id')->index();
            $table->string('token')->unique();
            $table->string('purpose')->default('magic-link');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_access_tokens');
    }
};
