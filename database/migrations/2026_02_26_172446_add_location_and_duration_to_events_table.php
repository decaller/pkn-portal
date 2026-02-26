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
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('nation')->nullable();
            $table->integer('duration_days')->nullable();
            $table->string('google_maps_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['city', 'province', 'nation', 'duration_days', 'google_maps_url']);
        });
    }
};
