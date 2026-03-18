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
        if (! Schema::hasTable('documents')) {
            return;
        }

        Schema::table('documents', function (Blueprint $table) {
            $table->string('title')->nullable()->change();

            if (! Schema::hasColumn('documents', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('tags');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('documents')) {
            return;
        }

        Schema::table('documents', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();

            if (Schema::hasColumn('documents', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
        });
    }
};
