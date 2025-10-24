<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('follow_up_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments');
            $table->text('notes')->nullable();
            $table->enum('method', ['Call','Visit','SMS','WhatsApp','Email','Meeting'])->default('Call');
            $table->string('outcome', 255)->nullable();
            $table->enum('status', ['Pending','In Progress','Completed'])->default('Pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('follow_up_histories');
    }
};
