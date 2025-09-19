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
        Schema::create('coas', function (Blueprint $table) { $table->id(); 
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->string('nama_coa'); 
            $table->decimal('pagu', 15, 2)->default(0); 
            $table->decimal('sisa_pagu', 15, 2)->default(0); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coas');
    }
};
