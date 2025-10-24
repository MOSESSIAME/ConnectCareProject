<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained('churches')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['church_id', 'name']); // same name allowed in different churches, but unique within one church
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
