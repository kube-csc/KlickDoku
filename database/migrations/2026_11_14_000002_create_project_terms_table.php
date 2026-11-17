<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('placeholder_key');
            $table->string('value_de');
            $table->string('value_en')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'placeholder_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_terms');
    }
};
