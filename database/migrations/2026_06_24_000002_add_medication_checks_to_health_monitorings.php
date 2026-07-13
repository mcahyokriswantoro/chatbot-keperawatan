<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_monitorings', function (Blueprint $table) {
            $table->json('medication_checks')->nullable()->after('medication_on_time');
        });
    }

    public function down(): void
    {
        Schema::table('health_monitorings', function (Blueprint $table) {
            $table->dropColumn('medication_checks');
        });
    }
};
