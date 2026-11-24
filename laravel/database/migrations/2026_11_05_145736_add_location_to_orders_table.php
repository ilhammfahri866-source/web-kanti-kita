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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('customer_class');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->integer('distance_meters')->nullable()->after('longitude');
            $table->string('location_status', 50)->default('in_school')->after('distance_meters'); // 'in_school', 'outside', 'unverified'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'distance_meters', 'location_status']);
        });
    }
};
