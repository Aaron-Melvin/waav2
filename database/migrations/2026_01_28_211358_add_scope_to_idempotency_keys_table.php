<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::disableForeignKeyConstraints();
            Schema::table('idempotency_keys', function (Blueprint $table) {
                $table->dropUnique(['partner_id', 'key']);
                $table->dropIndex(['partner_id']);
            });
            Schema::rename('idempotency_keys', 'idempotency_keys_old');

            Schema::create('idempotency_keys', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('partner_id')->nullable()->index();
                $table->string('scope_type')->nullable();
                $table->uuid('scope_id')->nullable();
                $table->string('key');
                $table->string('request_hash');
                $table->json('response')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['scope_type', 'scope_id']);
                $table->unique(['scope_type', 'scope_id', 'key']);
            });

            DB::table('idempotency_keys')->insertUsing(
                [
                    'id',
                    'partner_id',
                    'scope_type',
                    'scope_id',
                    'key',
                    'request_hash',
                    'response',
                    'status',
                    'expires_at',
                    'created_at',
                    'updated_at',
                ],
                DB::table('idempotency_keys_old')->select([
                    'id',
                    'partner_id',
                    DB::raw("'App\\\\Models\\\\Partner'"),
                    'partner_id',
                    'key',
                    'request_hash',
                    'response',
                    'status',
                    'expires_at',
                    'created_at',
                    'updated_at',
                ])
            );

            Schema::drop('idempotency_keys_old');
            Schema::enableForeignKeyConstraints();

            return;
        }

        Schema::table('idempotency_keys', function (Blueprint $table) {
            $table->uuid('partner_id')->nullable()->change();
            $table->string('scope_type')->nullable()->after('partner_id');
            $table->uuid('scope_id')->nullable()->after('scope_type');
            $table->index(['scope_type', 'scope_id']);
            $table->unique(['scope_type', 'scope_id', 'key']);
            $table->dropUnique(['partner_id', 'key']);
        });

        DB::table('idempotency_keys')->update([
            'scope_type' => DB::raw("'App\\\\Models\\\\Partner'"),
            'scope_id' => DB::raw('partner_id'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::disableForeignKeyConstraints();
            Schema::table('idempotency_keys', function (Blueprint $table) {
                $table->dropUnique(['scope_type', 'scope_id', 'key']);
                $table->dropIndex(['scope_type', 'scope_id']);
                $table->dropIndex(['partner_id']);
            });
            Schema::rename('idempotency_keys', 'idempotency_keys_new');

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

            DB::table('idempotency_keys')->insertUsing(
                [
                    'id',
                    'partner_id',
                    'key',
                    'request_hash',
                    'response',
                    'status',
                    'expires_at',
                    'created_at',
                    'updated_at',
                ],
                DB::table('idempotency_keys_new')->select([
                    'id',
                    'partner_id',
                    'key',
                    'request_hash',
                    'response',
                    'status',
                    'expires_at',
                    'created_at',
                    'updated_at',
                ])
            );

            Schema::drop('idempotency_keys_new');
            Schema::enableForeignKeyConstraints();

            return;
        }

        Schema::table('idempotency_keys', function (Blueprint $table) {
            $table->dropUnique(['scope_type', 'scope_id', 'key']);
            $table->dropIndex(['scope_type', 'scope_id']);
            $table->dropColumn(['scope_type', 'scope_id']);
            $table->uuid('partner_id')->nullable(false)->change();
            $table->unique(['partner_id', 'key']);
        });
    }
};
