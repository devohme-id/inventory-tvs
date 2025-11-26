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
        Schema::create('picking_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('so_item_id');
            $table->unsignedBigInteger('bind_id')->nullable();
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->integer('quantity_to_pick')->nullable(false);
            $table->enum('status', ['Pending', 'Picked'])->nullable(false)->default('Pending');
            $table->timestamp('picked_at')->nullable(true)->default(null);
            $table->foreign('so_item_id')->references('id')->on('so_items'); // Kade Patuker Jeung SO Items
            $table->foreign('bind_id')->references('id')->on('storage_bins');
            $table->foreign('operator_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picking_tasks');
    }
};
