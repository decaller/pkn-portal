<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("invoice_items", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("invoice_id")->constrained()->cascadeOnDelete();
            $table->string("package_name");
            $table->unsignedInteger("participant_count")->default(1);
            $table->decimal("unit_price", 12, 2)->default(0);
            $table->decimal("line_total", 12, 2)->default(0);
            $table->json("metadata")->nullable();
            $table->timestamps();

            $table->index("invoice_id");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("invoice_items");
    }
};
