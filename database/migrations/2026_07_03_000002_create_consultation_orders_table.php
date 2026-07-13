<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider_key', 50);
            $table->unsignedInteger('amount');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('total_paid');
            $table->foreignId('consultation_voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('voucher_code', 50)->nullable();
            $table->string('status', 20)->default('paid');
            $table->string('payment_method', 30)->default('simulation');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'provider_key', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_orders');
    }
};
