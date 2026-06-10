<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ayosehat_articles', function (Blueprint $table) {
            $table->id();
            $table->string('external_slug')->unique();
            $table->string('category_id', 50);
            $table->string('category_name', 100);
            $table->string('title');
            $table->text('excerpt');
            $table->string('url');
            $table->string('image_url')->nullable();
            $table->string('tag', 80)->nullable();
            $table->unsignedSmallInteger('read_min')->nullable();
            $table->date('published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayosehat_articles');
    }
};
