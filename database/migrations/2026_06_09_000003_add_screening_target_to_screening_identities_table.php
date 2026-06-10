<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screening_identities', function (Blueprint $table) {
            $table->string('screening_target', 20)->default('self')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('screening_identities', function (Blueprint $table) {
            $table->dropColumn('screening_target');
        });
    }
};
