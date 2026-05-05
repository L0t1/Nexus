<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add composite index used by inbox filtering (direction + status) and
     * outbound message status-update queries in MessageProcessor.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['conversation_id', 'direction', 'status'], 'messages_conv_direction_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_conv_direction_status_index');
        });
    }
};
