<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            // Core fields used by the form/controller
            if (!Schema::hasColumn('communications', 'title')) {
                $table->string('title', 150)->after('id');
            }
            if (!Schema::hasColumn('communications', 'body')) {
                $table->longText('body')->after('title');
            }
            if (!Schema::hasColumn('communications', 'channel')) {
                $table->string('channel', 20)->after('body'); // sms | whatsapp | email
            }
            if (!Schema::hasColumn('communications', 'audience')) {
                $table->string('audience', 50)->after('channel'); // all | first_timers | new_converts | members | custom
            }
            if (!Schema::hasColumn('communications', 'filters')) {
                $table->json('filters')->nullable()->after('audience'); // {zone_id, district_id, homecell_id,...}
            }
            if (!Schema::hasColumn('communications', 'status')) {
                $table->string('status', 20)->default('queued')->after('filters'); // draft|queued|sent|failed
            }
            if (!Schema::hasColumn('communications', 'scheduled_at')) {
                $table->dateTime('scheduled_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('communications', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('scheduled_at')
                    ->constrained('users')->nullOnDelete();
            }

            // Timestamps if missing
            if (!Schema::hasColumn('communications', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            // Roll back only the columns we added (check before drop to be safe)
            foreach (['title','body','channel','audience','filters','status','scheduled_at','created_by'] as $col) {
                if (Schema::hasColumn('communications', $col)) {
                    if ($col === 'created_by') {
                        $table->dropConstrainedForeignId('created_by');
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });
    }
};
