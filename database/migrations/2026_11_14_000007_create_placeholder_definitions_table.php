<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placeholder_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('description_de');
            $table->text('description_en')->nullable();
            $table->string('default_value_de');
            $table->string('default_value_en')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placeholder_definitions');
    }
};
