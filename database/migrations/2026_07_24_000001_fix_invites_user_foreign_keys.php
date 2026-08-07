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
        Schema::table('invites', function (Blueprint $table) {
            $table->dropForeign('invites_created_by_foreign');
            $table->dropForeign('invites_invited_user_id_foreign');
            $table->unsignedBigInteger('created_by')->nullable()->change();
        });

        Schema::table('invites', function (Blueprint $table) {
            // Deleting a user shouldn't be blocked by invites they created or
            // redeemed — the invite record is historical and should survive,
            // just with an unknown creator/redeemer.
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('invited_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            $table->dropForeign('invites_created_by_foreign');
            $table->dropForeign('invites_invited_user_id_foreign');
        });

        Schema::table('invites', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('invited_user_id')->references('id')->on('users');
        });
    }
};
