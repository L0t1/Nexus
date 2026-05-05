<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Fix payment_status enum mismatch.
 *
 * The original migration defined: ['pending', 'collected', 'returned', 'refunded']
 * but OrderController and all business logic uses: ['pending', 'paid', 'failed', 'refunded']
 *
 * MySQL requires a special approach to modify ENUMs.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending'");
        }
        // SQLite has no native ENUM; text column accepts any value, no DDL change needed
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','collected','returned','refunded') NOT NULL DEFAULT 'pending'");
        }
    }
};
