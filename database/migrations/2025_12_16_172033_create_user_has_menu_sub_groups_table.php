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
        Schema::create('user_has_menu_sub_groups', function (Blueprint $table) {
            $table->id('id');
            $table->string('role_id', 16);
            $table->foreign('role_id')->references('id')->on('roles');
            $table->string('menu_sub_group_id', 16);
            $table->foreign('menu_sub_group_id')->references('id')->on('menu_sub_groups');
            $table->tinyInteger('allow_view')->default(0);
            $table->tinyInteger('allow_create')->default(0);
            $table->tinyInteger('allow_update')->default(0);
            $table->tinyInteger('allow_delete')->default(0);
            $table->tinyInteger('allow_print')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_menu_sub_groups');
    }
};
