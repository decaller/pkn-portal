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
        Schema::table("event_registrations", function (Blueprint $table): void {
            $table->string("package_name")->nullable()->after("organization_id");
            $table->unsignedInteger("participant_count")->default(1)->after("package_name");
            $table->decimal("unit_price", 12, 2)->default(0)->after("participant_count");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("event_registrations", function (Blueprint $table): void {
            $table->dropColumn(["package_name", "participant_count", "unit_price"]);
        });
    }
};
