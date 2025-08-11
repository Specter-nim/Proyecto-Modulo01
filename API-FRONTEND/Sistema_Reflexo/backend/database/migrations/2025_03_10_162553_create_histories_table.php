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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->boolean('testimony')->nullable();
            $table->text('private_observation')->nullable();
            $table->text('observation')->nullable();
            $table->decimal('height',7,3)->nullable();
            $table->decimal('weight',6,3)->nullable();
            $table->decimal('last_weight',6,3)->nullable();
            $table->boolean('menstruation')->nullable();
            $table->string('diu_type', 255)->nullable();     
            $table->boolean('gestation')->nullable();
            $table->foreignId('patient_id')->nullable()->constrained('patients');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
