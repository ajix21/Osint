<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah foreign key search_logs.user_id dari CASCADE DELETE
     * menjadi SET NULL sehingga audit trail tidak ikut terhapus
     * ketika user dihapus (soft delete).
     */
    public function up(): void
    {
        Schema::table('search_logs', function (Blueprint $table) {
            // Drop existing FK constraint
            $table->dropForeign(['user_id']);

            // Buat ulang kolom sebagai nullable
            $table->foreignId('user_id')->nullable()->change();

            // Tambah FK baru dengan SET NULL on delete
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('search_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->foreignId('user_id')->nullable(false)->change();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
        });
    }
};
