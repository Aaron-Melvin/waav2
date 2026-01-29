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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('customer_id')->nullable()->index();
            $table->string('status')->default('draft');
            $table->string('channel')->default('direct');
            $table->string('currency', 3)->default('EUR');
            $table->decimal('total_gross', 10, 2)->default(0);
            $table->decimal('total_tax', 10, 2)->default(0);
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->string('payment_status')->default('unpaid');
            $table->string('booking_reference')->nullable()->unique();
            $table->string('terms_version')->nullable();
            $table->json('meta')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('bookings');
    }
};
