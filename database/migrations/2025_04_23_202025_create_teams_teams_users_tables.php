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
        // Migration: team domains
        Schema::create('team_domains', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'devops', 'product'
            $table->timestamps();
        });

        // Migration: team frameworks
        Schema::create('team_frameworks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'private', 'public'
            $table->timestamps();
        });

        // Migration: teams
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('team_domain_id')->constrained('team_domains')->onDelete('cascade');
            $table->foreignId('team_framework_id')->constrained('team_frameworks')->onDelete('cascade');
            $table->string('framework')->nullable(); // Framework for teams (e.g., "Scrum", "Agile")
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });


// Migration: team_user (many-to-many with roles)
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member'); // member, lead, coach, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop dependent tables first
        Schema::dropIfExists('team_user'); // Drop the pivot table first
        Schema::dropIfExists('teams'); // Drop `teams` table next
        Schema::dropIfExists('team_frameworks'); // Drop `team_frameworks` table
        Schema::dropIfExists('team_domains'); // Finally, drop `team_domains` table

    }
};
