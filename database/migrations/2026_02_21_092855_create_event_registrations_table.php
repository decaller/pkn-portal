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
        Schema::create("event_registrations", function (Blueprint $table) {
            $table->id();
            $table->foreignId("event_id")->constrained()->cascadeOnDelete();
            $table
                ->foreignId("organization_id")
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table
                ->foreignId("booker_user_id")
                ->constrained("users")
                ->cascadeOnDelete();

            $table->string("status")->default("draft");
            $table->string("payment_status")->default("unpaid");
            $table->decimal("total_amount", 12, 2)->default(0);
            $table->string("payment_proof_path")->nullable();
            $table->text("notes")->nullable();
            $table
                ->foreignId("verified_by_user_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();
            $table->timestamp("verified_at")->nullable();
            $table->timestamps();

            $table->index(["event_id", "status"]);
            $table->index(["booker_user_id", "payment_status"]);
            $table->index("organization_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("event_registrations");
    }
};
