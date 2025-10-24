<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('homecells', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('zone_id')->constrained('zones');
            $table->foreignId('leader_id')->nullable()->constrained('users');
            $table->string('provider_name', 100)->nullable();
            $table->string('provider_phone', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('homecells');
    }
};
