<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("invoices", function (Blueprint $table): void {
            $table->id();
            $table
                ->foreignId("event_registration_id")
                ->constrained("event_registrations")
                ->cascadeOnDelete();

            $table->string("invoice_number")->unique();
            $table->unsignedInteger("version")->default(1);
            $table->string("status")->default("issued");

            $table->timestamp("issued_at");
            $table->date("due_at")->nullable();
            $table->string("currency", 3)->default("IDR");

            $table->json("event_snapshot")->nullable();
            $table->json("organization_snapshot")->nullable();
            $table->json("booker_snapshot")->nullable();

            $table->decimal("subtotal", 12, 2)->default(0);
            $table->decimal("discount_amount", 12, 2)->default(0);
            $table->decimal("tax_amount", 12, 2)->default(0);
            $table->decimal("total_amount", 12, 2)->default(0);

            $table->text("notes")->nullable();
            $table->timestamp("voided_at")->nullable();
            $table->string("void_reason")->nullable();

            $table->timestamps();

            $table->index(["event_registration_id", "status"]);
            $table->unique(["event_registration_id", "version"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("invoices");
    }
};
