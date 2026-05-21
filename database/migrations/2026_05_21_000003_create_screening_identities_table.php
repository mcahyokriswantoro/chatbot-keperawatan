<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('disease', 50);
            $table->string('name');
            $table->string('gender', 20);
            $table->string('phone', 20);
            $table->date('date_of_birth');
            $table->unsignedTinyInteger('age');
            $table->decimal('weight_kg', 5, 1);
            $table->unsignedSmallInteger('height_cm');
            $table->text('domicile_address');
            $table->string('occupation');
            $table->text('address');
            $table->string('district');
            $table->string('regency');
            $table->timestamps();
        });

        Schema::table('screening_sessions', function (Blueprint $table) {
            $table->foreignId('screening_identity_id')
                ->nullable()
                ->after('user_id')
                ->constrained('screening_identities')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('screening_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('screening_identity_id');
        });

        Schema::dropIfExists('screening_identities');
    }
};
