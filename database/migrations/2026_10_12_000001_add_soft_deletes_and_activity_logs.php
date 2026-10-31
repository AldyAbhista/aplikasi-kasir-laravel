<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Soft Deletes untuk transaksi
        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Activity Log / Audit Trail
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action'); // login, logout, create, update, delete, void, reset_password, dll
                $table->string('model_type')->nullable(); // App\Models\Transaction, dll
                $table->unsignedBigInteger('model_id')->nullable();
                $table->text('description');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index('action');
                $table->index('created_at');
            });
        }

        // Password Reset Tokens (untuk fitur lupa password yg aman)
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('activity_logs');
    }
};
