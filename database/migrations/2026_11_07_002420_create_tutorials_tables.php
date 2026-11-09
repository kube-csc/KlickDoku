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
        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('thumbnail_path')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->integer('view_count')->default(0);
            $table->integer('completion_count')->default(0);
            $table->timestamps();
        });

        Schema::create('tutorial_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutorial_id')->constrained()->cascadeOnDelete();
            $table->integer('step_number');
            $table->string('title');
            $table->text('instruction');
            $table->string('screenshot_path');
            $table->integer('screenshot_width');
            $table->integer('screenshot_height');
            $table->text('tip')->nullable();
            $table->text('warning')->nullable();
            $table->boolean('auto_advance')->default(false);
            $table->integer('auto_advance_delay_ms')->default(2000);
            $table->timestamps();
        });

        Schema::create('tutorial_hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutorial_step_id')->constrained()->cascadeOnDelete();
            $table->enum('hotspot_type', ['click', 'hover', 'drag', 'type', 'wait']);
            $table->integer('position_x');
            $table->integer('position_y');
            $table->integer('radius')->default(20);
            $table->string('label')->nullable();
            $table->enum('cursor_type', ['arrow', 'pointer', 'hand', 'text', 'context_menu'])->default('pointer');
            $table->integer('click_count')->default(1);
            $table->boolean('is_clickable')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tutorial_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutorial_id')->constrained()->cascadeOnDelete();
            $table->foreignId('current_step_id')->nullable()->constrained('tutorial_steps')->nullOnDelete();
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_visited_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorial_progress');
        Schema::dropIfExists('tutorial_hotspots');
        Schema::dropIfExists('tutorial_steps');
        Schema::dropIfExists('tutorials');
    }
};
