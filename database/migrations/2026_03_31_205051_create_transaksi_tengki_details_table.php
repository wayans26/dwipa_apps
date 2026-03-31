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
        Schema::create('transaksi_tengki_details', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('transaksi_tengki_id');
            $table->foreign('transaksi_tengki_id')->references('id')->on('transaksi_tengkis');
            $table->date('tanggal');
            $table->integer('jumlah_ret')->nullable();
            $table->unsignedBigInteger('harga')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('type');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_tengki_details');
    }
};
