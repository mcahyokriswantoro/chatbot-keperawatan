<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homecare_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('price');
            $table->string('icon', 100)->default('👩‍⚕️');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('homecare_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('homecare_package_id')->constrained('homecare_packages')->onDelete('cascade');
            $table->string('reference_code', 40)->unique();
            $table->string('patient_name', 255);
            $table->string('patient_phone', 40);
            $table->dateTime('booking_date');
            $table->text('address');
            $table->string('sender_identity', 120)->nullable();
            $table->string('payment_proof')->nullable();
            $table->string('status', 20)->default('pending'); // pending, paid, completed, rejected
            $table->string('admin_note', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homecare_bookings');
        Schema::dropIfExists('homecare_packages');
    }
};
