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
        Schema::dropIfExists('campaign_subscriber');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('campaign_subscriber', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id');
            $table->unsignedBigInteger('campaign_id');

            $table->foreign('subscriber_id')
                  ->references('id')
                  ->on('subscribers');
            
            $table->foreign('campaign_id')
                  ->references('id')
                  ->on('campaigns');
                  
            $table->timestamps();
        });  
    }
};
