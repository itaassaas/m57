<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code')->unique();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 40);
            $table->string('shipping_address', 500);
            $table->string('shipping_city', 120);
            $table->string('shipping_state', 120);
            $table->string('shipping_locality')->nullable();
            $table->string('payment_type', 40);
            $table->string('payment_channel', 80)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('subtotal_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status', 40)->default('created');
            $table->json('hub_response')->nullable();
            $table->timestamps();
        });

        Schema::create('marketplace_order_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('owner_user_id');
            $table->string('owner_name');
            $table->unsignedBigInteger('hub_order_id')->nullable();
            $table->string('hub_order_number')->nullable();
            $table->string('status', 40)->default('pending');
            $table->decimal('subtotal_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->json('hub_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('marketplace_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_store_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->unsignedBigInteger('owner_user_id');
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->string('variation_name')->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->json('snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_order_items');
        Schema::dropIfExists('marketplace_order_stores');
        Schema::dropIfExists('marketplace_orders');
    }
};
