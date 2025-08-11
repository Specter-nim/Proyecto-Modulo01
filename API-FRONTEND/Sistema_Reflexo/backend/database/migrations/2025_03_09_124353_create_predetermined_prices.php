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
        Schema::create('predetermined_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('price', 8, 2); // 8 dígitos en total, 2 decimales
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predetermined_prices');
    }
};
