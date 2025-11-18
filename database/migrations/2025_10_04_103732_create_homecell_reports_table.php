<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('homecell_reports', function (Blueprint $table) {
            $table->id();

            // Optional relationships (we’ll leave as unsigned for now, since church/district tables may come later)
            $table->unsignedBigInteger('church_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();

            // Existing working relationships
            $table->foreignId('zone_id')->constrained('zones')->onDelete('cascade');
            $table->foreignId('homecell_id')->constrained('homecells')->onDelete('cascade');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');

            // Report date
            $table->date('report_date')->default(now());

            // Attendance fields
            $table->integer('males')->default(0);
            $table->integer('females')->default(0);
            $table->integer('first_timers')->default(0);
            $table->integer('new_converts')->default(0);

            // Testimonies or notes
             $table->text('testimonies')->nullable();
            $table->text('notes')->nullable();

            // Status tracking
            $table->enum('status', ['Submitted', 'Reviewed'])->default('Submitted');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('homecell_reports');
    }
};
