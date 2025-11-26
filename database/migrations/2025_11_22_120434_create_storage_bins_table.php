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
        Schema::create('storage_bins', function (Blueprint $table) {
            $table->id();
            $table->string('bin_code', 20)->nullable(false);
            $table->string('rack')->nullable(true)->default(null);
            $table->integer('level')->nullable(true)->default(null);
            $table->integer('slot')->nullable(true)->default(null);
            $table->string('bin_type', 20)->nullable(false)->default('Standard');
            $table->tinyInteger('is_empty')->nullable(false)->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_bins');
    }
};
