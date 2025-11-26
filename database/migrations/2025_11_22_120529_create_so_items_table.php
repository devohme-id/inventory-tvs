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
        Schema::create('so_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('so_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity_ordered')->nullable(false);
            $table->integer('quantity_picked')->nullable(true)->default(0);
            $table->integer('quantity_packed')->nullable(true)->default(0);
            $table->timestamps();

            $table->foreign('so_id')->references('id')->on('sales_orders'); // Kade Patuker Jeung SO Items
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('so_items');
    }
};
