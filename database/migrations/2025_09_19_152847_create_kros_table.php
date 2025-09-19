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
        Schema::create('kros', function (Blueprint $table) { 
            $table->id(); $table->foreignId('activity_id')->constrained()->onDelete('cascade'); 
            $table->string('nama_kro'); 
            $table->string('lokasi')->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kros');
    }
};
