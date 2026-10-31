<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('store_settings')
            ->where('store_name', 'Taichan')
            ->update(['store_name' => 'Kasir']);
    }

    public function down(): void
    {
        // Existing branding may have been customized after this migration.
    }
};
