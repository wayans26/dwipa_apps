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
        Schema::create('menu_sub_groups', function (Blueprint $table) {
            $table->string('id', 16)->primary();
            $table->string('name', 50);
            $table->string('page_name', 50);
            $table->integer('order_no');
            $table->string('menu_group_id', 16);
            $table->foreign('menu_group_id')->references('id')->on('menu_groups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_sub_groups');
    }
};
