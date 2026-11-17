<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_guide_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guide_step_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('path');
            $table->string('caption')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['project_id', 'guide_step_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_guide_images');
    }
};
