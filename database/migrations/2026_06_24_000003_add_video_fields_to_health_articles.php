<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_articles', function (Blueprint $table) {
            $table->string('content_type', 20)->default('article')->after('category');
            $table->string('video_url', 500)->nullable()->after('cover_image');
        });
    }

    public function down(): void
    {
        Schema::table('health_articles', function (Blueprint $table) {
            $table->dropColumn(['content_type', 'video_url']);
        });
    }
};
