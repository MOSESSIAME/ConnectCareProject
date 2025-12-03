<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // modify to allow null (and remove any NOT NULL restriction)
            // if the column doesn't exist yet, use ->string('gender', 1)->nullable();
            $table->string('gender', 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // revert to NOT NULL with default 'M' (choose default you desire)
            $table->string('gender', 1)->nullable(false)->default('M')->change();
        });
    }
};
