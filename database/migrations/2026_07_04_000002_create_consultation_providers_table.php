<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_providers', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();
            $table->string('category_key', 50);
            $table->boolean('active')->default(true);
            $table->string('name');
            $table->string('short_name');
            $table->string('title')->nullable();
            $table->string('specialty')->nullable();
            $table->string('credential')->nullable();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->unsignedTinyInteger('rating_percent')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->string('photo')->nullable();
            $table->string('icon', 10)->nullable();
            $table->string('whatsapp', 20);
            $table->string('whatsapp_intl', 20)->nullable();
            $table->text('greeting')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category_key', 'active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_providers');
    }
};
