<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable(); // Platform-specific user ID (IG/WA)
            $table->string('platform')->nullable(); // instagram, whatsapp, facebook, manual
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('street_address')->nullable();
            $table->text('notes')->nullable();
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->timestamp('last_contacted_at')->nullable();
            $table->json('metadata')->nullable(); // Extra platform-specific data
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['merchant_id', 'external_id', 'platform']);
            $table->index(['merchant_id', 'phone']);
            $table->index(['merchant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
