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
        Schema::connection('sqlite')->create('also_stock', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unique();
            $table->bigInteger('stock');
            $table->bigInteger('price');
            $table->dateTime('available_next_date')->nullable();
            $table->bigInteger('available_next_quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlite')->dropIfExists('also_stock');
    }
};
