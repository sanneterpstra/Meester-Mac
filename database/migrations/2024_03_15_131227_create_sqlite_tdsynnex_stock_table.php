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
        Schema::connection('sqlite')->create('tdsynnex_stock', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unique();
            $table->bigInteger('stock');
            $table->dateTime('expected_delivery')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlite')->dropIfExists('tdsynnex_stock');
    }
};
