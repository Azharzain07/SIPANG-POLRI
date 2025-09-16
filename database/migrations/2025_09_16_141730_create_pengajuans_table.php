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
        Schema::create('pengajuans', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal_pengajuan');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->nullable()->comment('Ini untuk ID Bagian')->constrained('categories')->onDelete('set null');
        $table->string('judul');
        $table->text('deskripsi');
        $table->string('lampiran')->nullable();
        $table->decimal('jumlah_dana', 15, 2);
        $table->string('status')->default('pending');
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
