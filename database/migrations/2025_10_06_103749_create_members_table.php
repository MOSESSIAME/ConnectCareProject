<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('type', ['First-timer', 'New Convert', 'Existing Member']);
            $table->boolean('from_other_church')->default(false);
            $table->text('note')->nullable();
            $table->boolean('foundation_class_completed')->default(false);

            // ✅ Foreign key to service_units (BIGINT UNSIGNED matches service_units.id)
            $table->unsignedBigInteger('service_unit_id')->nullable();
            $table->foreign('service_unit_id')
                  ->references('id')->on('service_units')
                  ->onDelete('set null');

            // ✅ Foreign key to homecells
            $table->unsignedBigInteger('homecell_id')->nullable();
            $table->foreign('homecell_id')
                  ->references('id')->on('homecells')
                  ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('members');
    }
};
