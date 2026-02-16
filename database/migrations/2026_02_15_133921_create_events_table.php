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
            
            // Identity
            $table->string('title');
            $table->string('slug')->unique(); // Folder Name
            $table->text('description')->nullable();
            $table->date('event_date');
            
            // Media & Files (JSON Arrays)
            $table->string('cover_image')->nullable();
            $table->json('photos')->nullable();   // Gallery
            $table->json('files')->nullable();    // General Docs
            
            // THE RUNDOWN (Stores all sessions as JSON)
            $table->json('rundown')->nullable(); 

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
