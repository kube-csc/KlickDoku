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
        Schema::create('content_translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type'); // Guide, Tutorial, Page, Category
            $table->unsignedBigInteger('translatable_id');
            $table->string('locale', 5); // de, en
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();

            $table->unique(['translatable_type', 'translatable_id', 'locale']);
            $table->index(['translatable_type', 'translatable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_translations');
    }
};
