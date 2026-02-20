<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("documents", function (Blueprint $table) {
            $table->id();

            // 1. Linking (Traceability)
            // This tells us which Event and which specific Session "spawned" this file
            $table
                ->foreignId("event_id")
                ->nullable()
                ->constrained()
                ->onDelete("cascade");
            $table->string("session_slug")->nullable();

            // 2. Identity
            $table->string("title");
            $table->string("slug")->unique();
            $table->jsonb("tags")->nullable();

            // 3. Storage
            $table->string("file_path")->unique();
            $table->string("original_filename")->nullable();

            // 4. APACHE TIKA COLUMNS (The "Brain" storage)
            // longText is necessary because some PDFs can be hundreds of pages
            $table->longText("content")->nullable();
            $table->string("mime_type")->nullable(); // e.g. application/pdf, image/jpeg
            $table->json("metadata")->nullable(); // Stores Tika's technical meta (author, date, etc.)

            // 5. Status
            $table->text("description")->nullable();
            $table->boolean("is_active")->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("documents");
    }
};
