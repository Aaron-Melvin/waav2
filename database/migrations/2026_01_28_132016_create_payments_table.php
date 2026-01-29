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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('booking_id')->index();
            $table->string('provider')->default('manual');
            $table->string('provider_payment_id')->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('pending');
            $table->timestamp('captured_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('booking_id')->references('id')->on('bookings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
