<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->integer('males')->default(0);
            $table->integer('females')->default(0);
            $table->integer('children')->default(0);
            $table->integer('first_timers')->default(0);
            $table->integer('new_converts')->default(0);
            $table->decimal('offering', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_attendances');
    }
};
