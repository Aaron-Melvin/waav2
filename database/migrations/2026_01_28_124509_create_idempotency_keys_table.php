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
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->string('key');
            $table->string('request_hash');
            $table->json('response')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['partner_id', 'key']);
            $table->foreign('partner_id')->references('id')->on('partners');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
