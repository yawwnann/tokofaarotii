<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('origin_district_id', 12);
            $table->string('destination_district_id', 12);
            $table->decimal('rate', 12, 2);
            $table->timestamps();

            $table->foreign('origin_district_id')->references('id')->on('indonesia_districts')->onDelete('cascade');
            $table->foreign('destination_district_id')->references('id')->on('indonesia_districts')->onDelete('cascade');
            $table->unique(['origin_district_id', 'destination_district_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
