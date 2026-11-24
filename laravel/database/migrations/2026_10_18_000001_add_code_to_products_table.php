<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom code (kode produk unik) ke tabel products.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'code')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('code', 50)->nullable()->after('tenant_id');
            });
        }

        // Isi kode produk otomatis untuk data yang sudah ada di database
        $products = DB::table('products')->orderBy('id')->get();
        foreach ($products as $prod) {
            if (empty($prod->code)) {
                $code = 'PRD-' . str_pad((string)$prod->id, 3, '0', STR_PAD_LEFT);
                DB::table('products')->where('id', $prod->id)->update(['code' => $code]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'code')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
};
