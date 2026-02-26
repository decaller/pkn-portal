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
        Schema::table('events', function (Blueprint $table) {
            $table->string('place')->nullable();
            $table->string('proposal')->nullable();
            $table->json('documentation')->nullable();
            $table->unsignedBigInteger('survey_template_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['place', 'proposal', 'documentation', 'survey_template_id']);
        });
    }
};
