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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->index();
            $table->uuid('booking_id')->nullable()->index();
            $table->string('number')->unique();
            $table->string('currency', 3)->default('EUR');
            $table->decimal('total_gross', 10, 2)->default(0);
            $table->decimal('total_tax', 10, 2)->default(0);
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->string('status')->default('issued');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->string('pdf_url')->nullable();
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
        Schema::dropIfExists('invoices');
    }
};
