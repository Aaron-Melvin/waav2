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
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('webhook_id')->index();
            $table->string('event');
            $table->json('payload');
            $table->string('status')->default('pending');
            $table->unsignedInteger('attempt_count')->default(0);
            $table->text('last_error')->nullable();
            $table->integer('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->foreign('webhook_id')->references('id')->on('webhooks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
