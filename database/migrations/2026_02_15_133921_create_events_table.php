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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            
            // The basics
            $table->string('title'); // "Graduation 2026"
            
            // THE MOST IMPORTANT COLUMN:
            // This will be the folder name: "graduation-2026"
            $table->string('slug')->unique(); 
            
            $table->text('description')->nullable();
            $table->date('event_date');
            
            // Cover image for the dashboard card
            $table->string('cover_image')->nullable();
            
            // Helper to quickly find where files are stored
            // Example: "events/graduation-2026"
            $table->string('storage_path')->nullable(); 
            
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
