<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('category', 100);
            $table->integer('price');
            $table->integer('stock')->default(0);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('medicine_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('reference_code', 40)->unique();
            $table->integer('total_amount');
            $table->text('address');
            $table->string('sender_identity', 120)->nullable();
            $table->string('payment_proof')->nullable();
            $table->string('status', 20)->default('pending'); // pending, paid, delivered, rejected
            $table->string('admin_note', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('medicine_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_order_id')->constrained('medicine_orders')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_order_items');
        Schema::dropIfExists('medicine_orders');
        Schema::dropIfExists('medicines');
    }
};
