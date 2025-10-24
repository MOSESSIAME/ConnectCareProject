<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('channel', ['SMS','Email','WhatsApp']);
            $table->string('template_name', 100)->nullable();
            $table->text('content');
            $table->enum('status', ['Queued','Sent','Failed'])->default('Queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('communications');
    }
};
