<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('shipping_rates');
    }

    public function down(): void
    {
        Schema::create('shipping_rates', function ($table) {
            $table->id();
            $table->string('origin_district_id', 10);
            $table->string('destination_district_id', 10);
            $table->decimal('rate', 12, 2);
            $table->timestamps();

            $table->foreign('origin_district_id')
                ->references('id')->on('indonesia_districts')
                ->cascadeOnDelete();

            $table->foreign('destination_district_id')
                ->references('id')->on('indonesia_districts')
                ->cascadeOnDelete();
        });
    }
};
