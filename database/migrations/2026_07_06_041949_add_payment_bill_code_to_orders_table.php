<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('orders', 'payment_bill_code')) {
            Schema::table('orders', function (Blueprint $col) {
                $col->string('payment_bill_code')->nullable()->after('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('orders', 'payment_bill_code')) {
            Schema::table('orders', function (Blueprint $col) {
                $col->dropColumn('payment_bill_code');
            });
        }
    }
};
