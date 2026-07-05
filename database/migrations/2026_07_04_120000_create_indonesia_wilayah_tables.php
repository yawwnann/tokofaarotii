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
        Schema::create('indonesia_provinces', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('name', 255);
        });

        Schema::create('indonesia_regencies', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('province_id', 10);
            $table->string('name', 255);

            $table->foreign('province_id')->references('id')->on('indonesia_provinces')->onDelete('cascade');
            $table->index('province_id');
        });

        Schema::create('indonesia_districts', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            $table->string('regency_id', 10);
            $table->string('name', 255);

            $table->foreign('regency_id')->references('id')->on('indonesia_regencies')->onDelete('cascade');
            $table->index('regency_id');
        });

        Schema::create('indonesia_villages', function (Blueprint $table) {
            $table->string('id', 14)->primary();
            $table->string('district_id', 12);
            $table->string('name', 255);

            $table->foreign('district_id')->references('id')->on('indonesia_districts')->onDelete('cascade');
            $table->index('district_id');
        });

        // Tambahkan district_id dan village_id ke user_addresses
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('district_id')->nullable()->after('city_id');
            $table->string('village_id')->nullable()->after('district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['district_id', 'village_id']);
        });

        Schema::dropIfExists('indonesia_villages');
        Schema::dropIfExists('indonesia_districts');
        Schema::dropIfExists('indonesia_regencies');
        Schema::dropIfExists('indonesia_provinces');
    }
};
