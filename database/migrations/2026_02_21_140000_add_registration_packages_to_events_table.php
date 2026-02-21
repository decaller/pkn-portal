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
        Schema::table("events", function (Blueprint $table): void {
            $table->json("registration_packages")->nullable()->after("allow_registration");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("events", function (Blueprint $table): void {
            $table->dropColumn("registration_packages");
        });
    }
};
