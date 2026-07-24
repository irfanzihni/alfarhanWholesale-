<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variations', 'weight')) {
                // Weight in kg for this specific variation (e.g. 5kg pack = 5.00)
                // NULL means inherit from parent product's weight
                $table->decimal('weight', 8, 2)->nullable()->after('stock')
                      ->comment('Weight in kg for this variation. NULL = inherit from product.');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
