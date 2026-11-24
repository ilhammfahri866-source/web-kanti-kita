<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom code (kode lapak / stand tenant) ke tabel tenants.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('tenants', 'code')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('code', 50)->nullable()->after('user_id');
            });
        }

        // Isi kode tenant otomatis untuk data yang sudah ada di database
        $tenants = DB::table('tenants')->orderBy('id')->get();
        foreach ($tenants as $t) {
            if (empty($t->code)) {
                $code = 'TNT-' . str_pad((string)$t->id, 3, '0', STR_PAD_LEFT);
                DB::table('tenants')->where('id', $t->id)->update(['code' => $code]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tenants', 'code')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
};
