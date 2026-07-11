<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Map old system roles → new organizational roles
        $map = [
            'admin' => 'lead',
            'coach' => 'member',
        ];

        foreach ($map as $old => $new) {
            DB::table('team_user')->where('role', $old)->update(['role' => $new]);
            DB::table('team_invitations')->where('role', $old)->update(['role' => $new]);
        }
    }

    public function down(): void
    {
        $map = [
            'lead'   => 'admin',
        ];

        foreach ($map as $old => $new) {
            DB::table('team_user')->where('role', $old)->update(['role' => $new]);
            DB::table('team_invitations')->where('role', $old)->update(['role' => $new]);
        }
    }
};
