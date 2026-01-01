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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_id')
                ->nullable()
                ->constrained('barang')
                ->nullOnDelete();

            $table->integer('qty');
            $table->enum('jenis_transaksi', ['masuk', 'keluar']);

            $table->integer('stok_sebelum')->nullable();
            $table->integer('stok_sesudah')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
