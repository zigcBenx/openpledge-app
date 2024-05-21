<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programming_languageables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programming_language_id');
            $table->unsignedBigInteger('programming_languageable_id');
            $table->string('programming_languageable_type');
            $table->timestamps();

            $table->foreign('programming_language_id')
                ->references('id')
                ->on('programming_languages')
                ->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programming_languageables');
    }
};
