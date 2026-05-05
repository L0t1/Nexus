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
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('order_number')->unique();

            // Customer shipping info (snapshot at order time)
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('shipping_city')->nullable();
            $table->string('shipping_neighborhood')->nullable();
            $table->string('shipping_street')->nullable();
            $table->text('shipping_notes')->nullable();

            // Product & pricing
            $table->json('items')->nullable(); // [{ name, qty, price }]
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Payment
            $table->enum('payment_method', ['cod', 'bank_transfer', 'online'])->default('cod');
            $table->enum('payment_status', ['pending', 'collected', 'returned', 'refunded'])->default('pending');

            // Logistics
            $table->string('courier_provider')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('waybill_path')->nullable(); // Path to PDF label
            $table->enum('fulfillment_status', [
                'draft',
                'confirmed',
                'processing',
                'dispatched',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'returned',
                'cancelled',
            ])->default('draft');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // AI-generated flag
            $table->boolean('is_ai_generated')->default(false);
            $table->json('ai_confidence')->nullable(); // { overall: 0.95, fields: { phone: 0.99, address: 0.87 } }

            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['merchant_id', 'fulfillment_status']);
            $table->index(['merchant_id', 'created_at']);
            $table->index(['merchant_id', 'payment_status']);
            $table->index('tracking_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
