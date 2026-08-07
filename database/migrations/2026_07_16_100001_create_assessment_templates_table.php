<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessment_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete(); // null = global/public template
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_public')->default(false);
            $table->foreignId('source_assessment_id')->nullable()->constrained('assessments')->nullOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_templates');
    }
};
