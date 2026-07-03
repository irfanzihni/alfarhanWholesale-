<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_bill_code')->nullable()->after('status');
            $table->string('payment_ref')->nullable()->after('payment_bill_code');
            $table->timestamp('paid_at')->nullable()->after('payment_ref');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_bill_code', 'payment_ref', 'paid_at']);
        });
    }
};
