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
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('sms_provider_id')->nullable()->index();
            $table->string('related_type')->nullable()->index();
            $table->uuid('related_id')->nullable()->index();
            $table->string('to');
            $table->string('from')->nullable();
            $table->text('body');
            $table->string('status')->default('queued');
            $table->string('provider_message_id')->nullable()->index();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('sms_provider_id')->references('id')->on('sms_providers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
