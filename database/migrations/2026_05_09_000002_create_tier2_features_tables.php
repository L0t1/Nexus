<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Feature 6: Invoice / Receipt PDF Generation ──────────────────
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('file_path')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->json('merchant_details')->nullable(); // snapshot of merchant info
            $table->json('customer_details')->nullable(); // snapshot of customer info
            $table->text('notes')->nullable();
            $table->timestamp('issued_at');
            $table->timestamp('due_at')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'created_at']);
        });

        // ── Feature 7: Discount & Coupon System ──────────────────────────
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50)->index();
            $table->string('name');
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2); // 10% or €10
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // cap for percentage
            $table->integer('usage_limit')->nullable(); // total uses allowed
            $table->integer('usage_count')->default(0);
            $table->integer('per_customer_limit')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['merchant_id', 'code']);
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_applied', 10, 2);
            $table->timestamps();

            $table->index(['coupon_id', 'contact_id']);
        });

        // Add discount fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('payment_method')->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('shipping_cost');
            $table->string('currency', 3)->default('EUR')->after('total');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('currency');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
        });

        // ── Feature 9: Customer Satisfaction (CSAT) Scoring ──────────────
        Schema::create('csat_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->tinyInteger('rating')->nullable(); // 1-5 stars
            $table->text('feedback')->nullable();
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending');
            $table->string('token', 64)->unique(); // for public access link
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'status']);
            $table->index(['merchant_id', 'rating']);
        });

        // ── Feature 10: Return & Refund Management ──────────────────────
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('return_number')->unique();
            $table->enum('reason', ['defective', 'wrong_item', 'not_as_described', 'changed_mind', 'late_delivery', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->enum('status', ['requested', 'approved', 'rejected', 'received', 'refunded', 'exchanged'])->default('requested');
            $table->enum('refund_method', ['original', 'store_credit', 'bank_transfer', 'cash'])->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->json('items')->nullable(); // which items are returned
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'status']);
        });

        // ── Feature 11: Multi-Currency & Tax — merchant settings ────────
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('default_currency', 3)->default('EUR')->after('address');
            $table->decimal('default_tax_rate', 5, 2)->default(0)->after('default_currency');
            $table->string('tax_label', 30)->default('VAT')->after('default_tax_rate');
            $table->boolean('tax_inclusive')->default(false)->after('tax_label');
        });

        // ── Feature 13: Team Activity & Performance Log ─────────────────
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // order.created, message.sent, status.changed, etc.
            $table->string('subject_type')->nullable(); // Order, Contact, Conversation, etc.
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'created_at']);
            $table->index(['merchant_id', 'user_id']);
            $table->index(['subject_type', 'subject_id']);
        });

        // ── Feature 14: Predictive Analytics — demand forecast cache ────
        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('product_name');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->date('forecast_date');
            $table->integer('predicted_demand')->default(0);
            $table->integer('actual_demand')->nullable();
            $table->decimal('confidence', 5, 2)->default(0); // 0-100%
            $table->string('trend')->default('stable'); // rising, falling, stable
            $table->json('factors')->nullable(); // contributing factors
            $table->timestamps();

            $table->unique(['merchant_id', 'product_name', 'forecast_date']);
            $table->index(['merchant_id', 'forecast_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demand_forecasts');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('csat_surveys');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('invoices');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'discount_amount', 'currency', 'tax_rate', 'tax_amount']);
        });

        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['default_currency', 'default_tax_rate', 'tax_label', 'tax_inclusive']);
        });
    }
};
