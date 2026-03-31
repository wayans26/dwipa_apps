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
        Schema::create('personal_tokens', function (Blueprint $table) {
            $table->id('id');
            $table->string('iduser', 16);
            $table->foreign('iduser')->references('id')->on('users');
            $table->string('token')->unique();
            $table->longText('device')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_tokens');
    }
};
