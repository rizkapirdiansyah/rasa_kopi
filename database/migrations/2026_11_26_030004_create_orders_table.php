<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->enum('shipping_type', ['pickup', 'delivery'])->default('pickup');
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->string('pickup_time')->nullable();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->string('payment_method_name');
            $table->enum('status', [
                'pending',
                'menunggu_pembayaran',
                'dibayar',
                'diproses',
                'siap_diambil',
                'dikirim',
                'selesai',
                'dibatalkan'
            ])->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
