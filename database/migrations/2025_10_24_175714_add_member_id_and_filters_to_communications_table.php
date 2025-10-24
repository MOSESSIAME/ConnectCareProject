<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            if (!Schema::hasColumn('communications', 'member_id')) {
                $table->unsignedBigInteger('member_id')->nullable()->after('audience');
            }
            if (!Schema::hasColumn('communications', 'filters')) {
                $table->json('filters')->nullable()->after('audience');
            }
        });
    }

    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->dropColumn(['member_id', 'filters']);
        });
    }
};
