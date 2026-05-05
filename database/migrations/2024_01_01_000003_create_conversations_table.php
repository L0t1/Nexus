<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // Agent
            $table->string('external_thread_id')->nullable(); // Platform thread/chat ID
            $table->enum('platform', ['instagram', 'whatsapp', 'facebook', 'manual'])->default('manual');
            $table->enum('status', ['active', 'pending', 'resolved', 'archived'])->default('active');
            $table->string('subject')->nullable();
            $table->text('last_message_preview')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_unread')->default(true);
            $table->integer('unread_count')->default(0);
            $table->json('labels')->nullable(); // Custom tags
            $table->timestamps();
            $table->softDeletes();

            $table->index(['merchant_id', 'status', 'last_message_at']);
            $table->index(['merchant_id', 'is_unread']);
            $table->index(['merchant_id', 'platform']);
            $table->unique(['merchant_id', 'external_thread_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
