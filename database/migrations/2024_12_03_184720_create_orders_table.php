<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_price', 10, 2);
            $table->enum('status', [
                'pending',
                'confirmed',
                'ready',
                'out_for_delivery',
                'delivered',
                'returned'
            ])->default('pending');
            $table->string('payment_method')->nullable();
            $table->enum('payment_status',['paid','unpaid'])->default('unpaid');
            $table->string('phone_number');
            $table->text('address_text')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
