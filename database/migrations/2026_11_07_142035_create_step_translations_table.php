<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Übersetzungen für Guide-Schritte und Tutorial-Schritte.
     */
    public function up(): void
    {
        // guide_steps_translations
        Schema::create('guide_steps_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_step_id')->constrained('guide_steps')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->longText('instruction')->nullable();
            $table->text('tip')->nullable();
            $table->text('warning')->nullable();
            $table->unique(['guide_step_id', 'locale']);
            $table->timestamps();
        });

        // tutorial_steps_translations
        Schema::create('tutorial_steps_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutorial_step_id')->constrained('tutorial_steps')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->longText('instruction')->nullable();
            $table->text('tip')->nullable();
            $table->text('warning')->nullable();
            $table->unique(['tutorial_step_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorial_steps_translations');
        Schema::dropIfExists('guide_steps_translations');
    }
};
