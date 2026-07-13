<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_monitorings', function (Blueprint $table) {
            $table->string('monitor_type', 20)->default('daily')->after('user_id');
            $table->string('disease', 50)->nullable()->after('monitor_type');
            $table->string('period_month', 7)->nullable()->after('disease');
            $table->json('complaint_answers')->nullable()->after('complaints');
            $table->unsignedSmallInteger('complaint_total')->nullable()->after('complaint_answers');
            $table->string('complaint_score_label', 20)->nullable()->after('complaint_total');
            $table->unsignedSmallInteger('medication_prescription_days')->nullable()->after('medication_schedule');
            $table->boolean('medication_on_time')->nullable()->after('medication_prescription_days');
            $table->decimal('medication_compliance_percent', 5, 1)->nullable()->after('medication_on_time');
            $table->string('medication_compliance_label', 20)->nullable()->after('medication_compliance_percent');
            $table->string('relapse_frequency', 30)->nullable()->after('medication_compliance_label');
            $table->unsignedTinyInteger('relapse_score')->nullable()->after('relapse_frequency');
            $table->string('relapse_score_label', 20)->nullable()->after('relapse_score');
            $table->json('self_management_answers')->nullable()->after('activities');
            $table->decimal('self_management_percent', 5, 1)->nullable()->after('self_management_answers');
            $table->string('self_management_score_label', 20)->nullable()->after('self_management_percent');
            $table->json('summary_data')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('health_monitorings', function (Blueprint $table) {
            $table->dropColumn([
                'monitor_type',
                'disease',
                'period_month',
                'complaint_answers',
                'complaint_total',
                'complaint_score_label',
                'medication_prescription_days',
                'medication_on_time',
                'medication_compliance_percent',
                'medication_compliance_label',
                'relapse_frequency',
                'relapse_score',
                'relapse_score_label',
                'self_management_answers',
                'self_management_percent',
                'self_management_score_label',
                'summary_data',
            ]);
        });
    }
};
