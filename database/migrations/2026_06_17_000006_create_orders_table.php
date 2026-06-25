<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // customer
            $table->string('order_type')->default('online'); // online, outdoor
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('delivery_address')->nullable();
            $table->decimal('total_amount', 8, 2);
            $table->decimal('discount_amount', 8, 2)->default(0.00);
            $table->decimal('final_amount', 8, 2);
            $table->string('coupon_code')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // e.g. outdoor sales agent who created it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
