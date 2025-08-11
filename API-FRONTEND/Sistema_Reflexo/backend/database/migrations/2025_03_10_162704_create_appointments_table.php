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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('appointment_date')->nullable();
            $table->time('appointment_hour')->nullable();
            $table->string('ailments', 1000)->nullable();            
            $table->string('diagnosis', 1000)->nullable();
            $table->string('surgeries', 1000)->nullable();
            $table->string('reflexology_diagnostics', 1000)->nullable();                     
            $table->string('medications', 255)->nullable();
            $table->string('observation', 255)->nullable();
            $table->date('initial_date')->nullable();
            $table->date('final_date')->nullable();
            $table->string('appointment_type')->nullable();
            $table->integer('room')->nullable();
            $table->boolean('social_benefit')->nullable();
            $table->decimal('payment', 8,2)->nullable();
            $table->integer('ticket_number')->nullable();
            $table->foreignId('appointment_status_id')->nullable()->constrained('appointment_statuses');
            $table->foreignId('payment_type_id')->nullable()->constrained('payment_types');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('therapist_id')->nullable()->constrained('therapists');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
