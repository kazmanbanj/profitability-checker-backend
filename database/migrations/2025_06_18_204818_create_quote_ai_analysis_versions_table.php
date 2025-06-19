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
        Schema::create('quote_ai_analysis_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')
                ->constrained()
                ->onDelete('cascade');
            $table->json('suggestions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_ai_analysis_versions');
    }
};
