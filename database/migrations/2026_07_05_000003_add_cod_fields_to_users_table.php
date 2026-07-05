<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('cod_rejection_count')->default(0)->after('role');
            $table->timestamp('cod_blocked_until')->nullable()->after('cod_rejection_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cod_rejection_count', 'cod_blocked_until']);
        });
    }
};
