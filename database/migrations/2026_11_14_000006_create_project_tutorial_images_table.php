<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tutorial_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutorial_step_id')->constrained()->cascadeOnDelete();
            $table->string('screenshot_path');
            $table->integer('screenshot_width')->nullable();
            $table->integer('screenshot_height')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'tutorial_step_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tutorial_images');
    }
};
