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
        Schema::table('tutorial_steps', function (Blueprint $table) {
            $table->string('screenshot_path')->nullable()->change();
            $table->integer('screenshot_width')->nullable()->change();
            $table->integer('screenshot_height')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutorial_steps', function (Blueprint $table) {
            $table->string('screenshot_path')->nullable(false)->change();
            $table->integer('screenshot_width')->nullable(false)->change();
            $table->integer('screenshot_height')->nullable(false)->change();
        });
    }
};
