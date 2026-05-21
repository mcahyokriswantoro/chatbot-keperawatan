<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screening_identities', function (Blueprint $table) {
            $table->string('province')->nullable()->after('address');
            $table->string('province_kode', 20)->nullable()->after('province');
            $table->string('regency_kode', 20)->nullable()->after('regency');
            $table->string('district_kode', 20)->nullable()->after('district');
        });
    }

    public function down(): void
    {
        Schema::table('screening_identities', function (Blueprint $table) {
            $table->dropColumn(['province', 'province_kode', 'regency_kode', 'district_kode']);
        });
    }
};
