<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If your DB supports JSON, convert `filters` to JSON
        Schema::table('communications', function (Blueprint $table) {
            $table->json('filters')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert back to text (or whatever it was before)
        Schema::table('communications', function (Blueprint $table) {
            $table->text('filters')->nullable()->change();
        });
    }
};
