<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_orders', function (Blueprint $table) {
            $table->integer('distance_km')->default(1)->nullable()->after('address');
            $table->integer('shipping_fee')->default(5000)->nullable()->after('distance_km');
        });
    }

    public function down(): void
    {
        Schema::table('medicine_orders', function (Blueprint $table) {
            $table->dropColumn(['distance_km', 'shipping_fee']);
        });
    }
};
