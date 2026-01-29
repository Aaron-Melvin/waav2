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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->string('name');
            $table->string('channel');
            $table->string('locale', 8)->default('en');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
