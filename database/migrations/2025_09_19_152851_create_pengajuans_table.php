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
        Schema::create('pengajuans', function (Blueprint $table) { $table->id(); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->date('tanggal_pengajuan'); 
            $table->enum('status_npwp', ['pending', 'diterima', 'ditolak'])->default('pending'); 
            $table->foreignId('npwp_user_id')->nullable(); 
            $table->timestamp('npwp_processed_at')->nullable(); 
            $table->enum('status_ppk', ['pending', 'diterima', 'ditolak'])->default('pending'); 
            $table->foreignId('ppk_user_id')->nullable(); 
            $table->timestamp('ppk_processed_at')->nullable(); 
            $table->text('uraian'); 
            $table->string('kppn')->default('096-Garut'); 
            $table->foreignId('sumber_dana_id')->constrained('sumber_danas'); 
            $table->foreignId('program_id')->constrained('programs'); 
            $table->foreignId('activity_id')->constrained('activities'); 
            $table->foreignId('kro_id')->constrained('kros'); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
