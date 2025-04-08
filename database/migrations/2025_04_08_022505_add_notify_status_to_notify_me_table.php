<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotifyStatusToNotifyMeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notify_me', function (Blueprint $table) {
            $table->boolean('notify_status')->default(true);  // Default to `true` (enabled)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notify_me', function (Blueprint $table) {
            $table->dropColumn('notify_status');
        });
    }
}
