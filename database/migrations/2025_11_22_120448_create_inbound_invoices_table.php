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
        Schema::create('inbound_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->nullable(false);
            $table->string('supplier', 100)->nullable(true)->default(null);
            $table->date('received_at')->nullable(false);
            $table->enum('status', ['Pending', 'Received', 'Stored'])->nullable(false)->default('Pending');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_invoices');
    }
};
