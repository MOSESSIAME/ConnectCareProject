<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            if (!Schema::hasColumn('communications', 'filters')) {
                $table->json('filters')->nullable()->after('audience');
            } else {
                $table->json('filters')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->text('filters')->nullable()->change();
        });
    }
};
