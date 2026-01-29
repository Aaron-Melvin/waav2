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
        Schema::create('notification_queues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('notification_template_id')->nullable()->index();
            $table->string('channel');
            $table->string('recipient')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->string('model_type')->nullable()->index();
            $table->uuid('model_id')->nullable()->index();
            $table->timestamp('scheduled_for')->nullable();
            $table->json('payload')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('notification_template_id')->references('id')->on('notification_templates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_queues');
    }
};
