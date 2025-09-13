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
        Schema::create('repository_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repository_id')->constrained()->onDelete('cascade');
            $table->json('allowed_labels')->nullable();
            $table->boolean('enable_donation_expiry')->default(false);
            $table->integer('default_expiry_days')->nullable();
            $table->decimal('min_donation_amount', 10, 2)->nullable();
            $table->decimal('max_donation_amount', 10, 2)->nullable();
            $table->timestamps();
            
            $table->unique('repository_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repository_settings');
    }
};
