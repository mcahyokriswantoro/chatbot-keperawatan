<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_orders', function (Blueprint $table) {
            $table->string('reference_code', 40)->nullable()->after('provider_key');
            $table->string('dana_phone', 20)->nullable()->after('payment_method');
            $table->timestamp('verified_at')->nullable()->after('paid_at');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable()->after('verified_by');

            $table->index(['status', 'created_at']);
            $table->unique('reference_code');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_orders', function (Blueprint $table) {
            $table->dropUnique(['reference_code']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['reference_code', 'dana_phone', 'verified_at', 'admin_note']);
        });
    }
};
