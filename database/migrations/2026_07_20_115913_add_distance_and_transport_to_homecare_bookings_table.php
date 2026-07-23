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
        Schema::table('homecare_bookings', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->decimal('distance_km', 6, 2)->nullable()->after('longitude');
            $table->integer('transport_fee')->nullable()->after('distance_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homecare_bookings', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'distance_km', 'transport_fee']);
        });
    }
};
