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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('bind_id')->nullable();
            $table->integer('quantity')->nullable(true)->default(0);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('bind_id')->references('id')->on('storage_bins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
