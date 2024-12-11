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
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete(); 
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready','out_for_delivery', 'delivered', 'cancelled'])
                  ->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->cascadeOnDelete();
            $table->text('address_text')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}

