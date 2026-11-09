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
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('version')->nullable();
            $table->string('project_ref')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });

        Schema::create('guide_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->integer('step_number');
            $table->string('title');
            $table->text('description');
            $table->text('tip')->nullable();
            $table->text('warning')->nullable();
            $table->timestamps();
        });

        Schema::create('guide_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_step_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guide_id')->nullable()->constrained()->nullOnDelete();
            $table->string('filename');
            $table->string('path');
            $table->string('caption')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_images');
        Schema::dropIfExists('guide_steps');
        Schema::dropIfExists('guides');
    }
};
