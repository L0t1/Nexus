<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // facebook, instagram, whatsapp
            $table->string('external_id')->nullable(); // Page ID or Phone ID
            $table->string('label')->nullable(); // e.g. "Nexus Fashion Page"
            $table->json('credentials')->nullable(); // Access tokens, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['merchant_id', 'platform', 'external_id'], 'merchant_platform_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
