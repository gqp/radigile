<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Random invite code
            $table->foreignId('created_by')->constrained('users'); // Admin/User who created this
            $table->foreignId('invited_user_id')->nullable()->constrained('users'); // User who used the invite
            $table->integer('max_uses')->default(1); // Maximum uses of this invite
            $table->integer('times_used')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
