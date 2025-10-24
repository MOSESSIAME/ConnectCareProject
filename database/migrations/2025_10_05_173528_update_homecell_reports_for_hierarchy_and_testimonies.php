<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('homecell_reports', function (Blueprint $table) {
            // Hierarchy refs (nullable to avoid breaking existing rows)
            if (!Schema::hasColumn('homecell_reports', 'church_id')) {
                $table->foreignId('church_id')->nullable()->after('homecell_id')->constrained('churches')->nullOnDelete();
            }
            if (!Schema::hasColumn('homecell_reports', 'district_id')) {
                $table->foreignId('district_id')->nullable()->after('church_id')->constrained('districts')->nullOnDelete();
            }
            if (!Schema::hasColumn('homecell_reports', 'zone_id')) {
                $table->foreignId('zone_id')->nullable()->after('district_id')->constrained('zones')->nullOnDelete();
            }

            // Add testimonies
            if (!Schema::hasColumn('homecell_reports', 'testimonies')) {
                $table->text('testimonies')->nullable()->after('new_converts');
            }

            // Drop offering if it exists
            if (Schema::hasColumn('homecell_reports', 'offering')) {
                $table->dropColumn('offering');
            }
        });
    }

    public function down(): void
    {
        Schema::table('homecell_reports', function (Blueprint $table) {
            // Re-add offering (if you ever rollback)
            if (!Schema::hasColumn('homecell_reports', 'offering')) {
                $table->decimal('offering', 10, 2)->nullable()->after('new_converts');
            }

            if (Schema::hasColumn('homecell_reports', 'testimonies')) {
                $table->dropColumn('testimonies');
            }

            if (Schema::hasColumn('homecell_reports', 'zone_id')) {
                $table->dropForeign(['zone_id']);
                $table->dropColumn('zone_id');
            }
            if (Schema::hasColumn('homecell_reports', 'district_id')) {
                $table->dropForeign(['district_id']);
                $table->dropColumn('district_id');
            }
            if (Schema::hasColumn('homecell_reports', 'church_id')) {
                $table->dropForeign(['church_id']);
                $table->dropColumn('church_id');
            }
        });
    }
};
