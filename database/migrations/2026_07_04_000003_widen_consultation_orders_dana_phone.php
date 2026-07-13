<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_orders', function (Blueprint $table) {
            $table->string('dana_phone', 120)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('consultation_orders', function (Blueprint $table) {
            $table->string('dana_phone', 20)->nullable()->change();
        });
    }
};
