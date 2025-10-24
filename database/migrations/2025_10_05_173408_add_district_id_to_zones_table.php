<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            if (!Schema::hasColumn('zones', 'district_id')) {
                $table->foreignId('district_id')->nullable()->after('name')->constrained('districts')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            if (Schema::hasColumn('zones', 'district_id')) {
                $table->dropForeign(['district_id']);
                $table->dropColumn('district_id');
            }
        });
    }
};
