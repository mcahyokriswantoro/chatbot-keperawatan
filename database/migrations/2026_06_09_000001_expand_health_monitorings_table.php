<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_monitorings', function (Blueprint $table) {
            $table->text('complaints')->nullable()->after('user_id');
            $table->string('medication_name')->nullable()->after('complaints');
            $table->string('medication_dose')->nullable()->after('medication_name');
            $table->string('medication_schedule')->nullable()->after('medication_dose');
            $table->text('activities')->nullable()->after('medication_schedule');
            $table->boolean('diet_compliant')->nullable()->after('activities');
            $table->text('diet_notes')->nullable()->after('diet_compliant');
            $table->decimal('temperature', 4, 1)->nullable()->after('heart_rate');
            $table->unsignedSmallInteger('respiratory_rate')->nullable()->after('temperature');
            $table->unsignedTinyInteger('oxygen_saturation')->nullable()->after('respiratory_rate');
        });
    }

    public function down(): void
    {
        Schema::table('health_monitorings', function (Blueprint $table) {
            $table->dropColumn([
                'complaints',
                'medication_name',
                'medication_dose',
                'medication_schedule',
                'activities',
                'diet_compliant',
                'diet_notes',
                'temperature',
                'respiratory_rate',
                'oxygen_saturation',
            ]);
        });
    }
};
