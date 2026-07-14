<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add payment_bill_code if it doesn't exist
            if (!Schema::hasColumn('orders', 'payment_bill_code')) {
                $table->string('payment_bill_code')->nullable()->after('id');
            }

            // Add payment_status if it doesn't exist
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('payment_bill_code');
            }

            // Add payment_url if it doesn't exist (to redirect user to ToyyibPay)
            if (!Schema::hasColumn('orders', 'payment_url')) {
                $table->string('payment_url')->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumnIfExists('payment_bill_code');
            $table->dropColumnIfExists('payment_status');
            $table->dropColumnIfExists('payment_url');
        });
    }
};
