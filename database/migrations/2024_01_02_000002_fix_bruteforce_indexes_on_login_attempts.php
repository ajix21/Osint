<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            // Index optimal untuk query brute-force per-IP
            $table->index(['ip_address', 'success', 'created_at'], 'idx_bruteforce_ip');
            // Index optimal untuk query brute-force per-username
            $table->index(['username', 'success', 'created_at'], 'idx_bruteforce_user');
        });
    }

    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropIndex('idx_bruteforce_ip');
            $table->dropIndex('idx_bruteforce_user');
        });
    }
};
