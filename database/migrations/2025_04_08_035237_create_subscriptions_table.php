<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->date('starts_at');
            $table->date('ends_at')->nullable(); // Allows open-ended subscriptions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Optional: Helps preserve historical data.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
