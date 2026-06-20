<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdToInvitesTable extends Migration
{
    public function up()
    {
        Schema::table('invites', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable();

            // Add foreign key constraint (adjust table name and column as necessary)
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('invites', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
}
