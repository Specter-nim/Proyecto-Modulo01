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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('document_number', 20)->nullable()->unique();
            $table->string('paternal_lastname', 255);            
            $table->string('maternal_lastname', 255)->nullable();
            $table->string('name', 255);
            $table->string('personal_reference', 255)->nullable();
            $table->date('birth_date')->nullable();
            $table->char('sex',1)->nullable();
            $table->string('primary_phone', 80)->nullable();
            $table->string('secondary_phone', 80)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('ocupation', 100)->nullable();
            $table->string('health_condition', 255)->nullable();
            $table->string('address', 255)->nullable();      
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->foreignId('province_id')->nullable()->constrained('provinces');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            $table->foreignId('document_type_id')->nullable()->constrained('document_types');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
