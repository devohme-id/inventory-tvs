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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('so_id');
            $table->string('box_id', 50)->nullable(false);
            $table->decimal('total_weight_kg', 10, 2)->nullable(false)->default(null);
            $table->unsignedBigInteger('operator_id');
            $table->timestamp('shipped_at');
            $table->timestamps();
            $table->foreign('so_id')->references('id')->on('sales_orders'); // Awas Pahili
            $table->foreign('operator_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
