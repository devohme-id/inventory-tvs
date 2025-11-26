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
        Schema::create('inbound_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity_expected')->nullable(false);
            $table->integer('quantity_received')->nullable(false);
            $table->unsignedBigInteger('bind_id')->nullable();
            $table->enum('status', ['Pending', 'Received', 'Stored'])->nullable(false)->default('Pending');
            $table->foreign('invoice_id')->references('id')->on('inbound_invoices');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('bind_id')->references('id')->on('storage_bins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_items');
    }
};
