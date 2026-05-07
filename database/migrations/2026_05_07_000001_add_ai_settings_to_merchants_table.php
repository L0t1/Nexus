<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            // Whether the merchant has AI auto-reply turned on
            $table->boolean('ai_enabled')->default(false)->after('onboarding_completed');
            // Rolling monthly usage counter (reset each calendar month)
            $table->unsignedInteger('ai_monthly_messages_count')->default(0)->after('ai_enabled');
            $table->timestamp('ai_messages_reset_at')->nullable()->after('ai_monthly_messages_count');
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['ai_enabled', 'ai_monthly_messages_count', 'ai_messages_reset_at']);
        });
    }
};
