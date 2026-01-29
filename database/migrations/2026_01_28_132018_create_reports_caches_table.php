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
        Schema::create('reports_cache', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->nullable()->index();
            $table->string('report_key')->index();
            $table->json('payload');
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->unique(['partner_id', 'report_key']);
            $table->foreign('partner_id')->references('id')->on('partners');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports_cache');
    }
};
