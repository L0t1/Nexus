<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Staff who sent reply
            $table->string('external_message_id')->nullable(); // Platform message ID
            $table->enum('direction', ['inbound', 'outbound'])->default('inbound');
            $table->enum('type', ['text', 'image', 'video', 'audio', 'document', 'location', 'sticker', 'system'])->default('text');
            $table->text('body')->nullable();
            $table->json('media')->nullable(); // { url, mime_type, file_size, thumbnail_url }
            $table->json('ai_extraction')->nullable(); // AI-parsed data: { name, phone, city, address, intent }
            $table->boolean('is_ai_processed')->default(false);
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index('external_message_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
