<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Kasir');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->text('footer_message')->nullable();
            $table->timestamps();
        });

        // Insert default initial row
        DB::table('store_settings')->insert([
            'store_name' => 'Kasir',
            'address' => 'Jl. Contoh Alamat No. 123',
            'phone' => '08123456789',
            'footer_message' => 'Terima Kasih! Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
