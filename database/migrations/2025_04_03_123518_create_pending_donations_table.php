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
        Schema::create('pending_donations', function (Blueprint $table) {
            $table->id();

            $table->integer('amount');
            $table->boolean('is_claimed')->default(false);
            $table->dateTime('claimed_at')->nullable();
            $table->foreignId('donation_id')
                ->constrained('donations')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_donations');
    }
};
