<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('screening_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('answers');
            $table->text('summary');
            $table->string('risk_level', 20)->default('low');
            $table->boolean('is_emergency')->default(false);
            $table->timestamps();
        });

        Schema::create('health_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('systolic')->nullable();
            $table->unsignedSmallInteger('diastolic')->nullable();
            $table->unsignedSmallInteger('heart_rate')->nullable();
            $table->decimal('blood_sugar', 5, 1)->nullable();
            $table->decimal('weight', 5, 1)->nullable();
            $table->text('notes')->nullable();
            $table->date('recorded_at');
            $table->timestamps();
        });

        Schema::create('self_management_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type', 50);
            $table->string('title');
            $table->text('notes')->nullable();
            $table->boolean('completed')->default(false);
            $table->date('scheduled_for')->nullable();
            $table->timestamps();
        });

        Schema::create('health_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 50);
            $table->text('excerpt');
            $table->longText('content');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_articles');
        Schema::dropIfExists('self_management_logs');
        Schema::dropIfExists('health_monitorings');
        Schema::dropIfExists('screening_sessions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
