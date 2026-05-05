<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->string('business_slug')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('country')->default('XK'); // Kosovo
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->enum('subscription_tier', ['starter', 'pro', 'ultimate'])->default('starter');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // courier API keys, notification prefs, etc.
            $table->json('meta_credentials')->nullable(); // encrypted Meta/WA tokens
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_active']);
            $table->index('subscription_tier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
