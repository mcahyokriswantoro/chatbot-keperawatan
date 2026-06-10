<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender', 20)->nullable()->after('email');
            $table->string('phone', 20)->nullable()->unique()->after('gender');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->unsignedTinyInteger('age')->nullable()->after('date_of_birth');
            $table->decimal('weight', 5, 1)->nullable()->after('age');
            $table->decimal('height', 5, 1)->nullable()->after('weight');
            $table->text('address')->nullable()->after('height');
            $table->string('occupation')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'phone',
                'date_of_birth',
                'age',
                'weight',
                'height',
                'address',
                'occupation',
            ]);
        });
    }
};
