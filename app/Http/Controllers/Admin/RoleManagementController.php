<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Ensure User model is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleManagementController extends Controller
{
    /**
     * Update the role of a user.
     */
    public function updateRole(Request $request, User $user)
    {
        $this->authorize('update-role'); // Ensure only authorized users can update roles

        // Validate the role input
        $request->validate([
            'role' => 'required|in:admin,creator,member'
        ]);

        // Store the old role for logging
        $oldRole = $user->role;

        // Update the user's role
        try {
            $user->update(['role' => $request->role]);

            // Log role change in an audit trail (optional)
            DB::table('audit_trail')->insert([
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $request->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Redirect back with a success message
            return back()->with('success', 'User role updated successfully!');

        } catch (\Exception $e) {
            // Handle any exceptions (e.g., database errors)
            return back()->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }
}
