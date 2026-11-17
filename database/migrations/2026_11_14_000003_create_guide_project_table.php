<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->boolean('primary')->default(false);
            $table->timestamps();
            $table->unique(['guide_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_project');
    }
};
