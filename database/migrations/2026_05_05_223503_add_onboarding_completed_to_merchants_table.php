<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false)->after('is_active');
        });

        // Mark all existing merchants as onboarded so they aren't forced through it
        \App\Models\Merchant::query()->update(['onboarding_completed' => true]);
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn('onboarding_completed');
        });
    }
};
