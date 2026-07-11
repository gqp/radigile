<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMemberRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminTeamMemberRoleController extends Controller
{
    public function index()
    {
        $roles = TeamMemberRole::orderBy('sort_order')->orderBy('name')->get();
        $availablePermissions = TeamMemberRole::$availablePermissions;
        return view('dashboard.admin.team-member-roles.index', compact('roles', 'availablePermissions'));
    }

    public function create()
    {
        $availablePermissions = TeamMemberRole::$availablePermissions;
        return view('dashboard.admin.team-member-roles.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'slug'          => 'nullable|string|max:50|unique:team_member_roles,slug|regex:/^[a-z0-9\-]+$/',
            'sort_order'    => 'nullable|integer|min:0',
            'is_default'    => 'boolean',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $data['slug']        = $data['slug'] ?? Str::slug($request->name);
        $data['sort_order']  = $data['sort_order'] ?? 0;
        $data['is_default']  = $request->boolean('is_default');
        $data['permissions'] = $request->input('permissions', []);

        if ($data['is_default']) {
            TeamMemberRole::where('is_default', true)->update(['is_default' => false]);
        }

        TeamMemberRole::create($data);

        return redirect()->route('admin.team-member-roles.index')
            ->with('success', "Role \"{$data['name']}\" created.");
    }

    public function edit(TeamMemberRole $teamMemberRole)
    {
        $availablePermissions = TeamMemberRole::$availablePermissions;
        return view('dashboard.admin.team-member-roles.edit', ['role' => $teamMemberRole, 'availablePermissions' => $availablePermissions]);
    }

    public function update(Request $request, TeamMemberRole $teamMemberRole)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'slug'          => ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9\-]+$/', Rule::unique('team_member_roles', 'slug')->ignore($teamMemberRole->id)],
            'sort_order'    => 'nullable|integer|min:0',
            'is_default'    => 'boolean',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $data['slug']        = $data['slug'] ?? Str::slug($request->name);
        $data['sort_order']  = $data['sort_order'] ?? 0;
        $data['is_default']  = $request->boolean('is_default');
        $data['permissions'] = $request->input('permissions', []);

        if ($data['is_default']) {
            TeamMemberRole::where('is_default', true)
                ->where('id', '!=', $teamMemberRole->id)
                ->update(['is_default' => false]);
        }

        $teamMemberRole->update($data);

        return redirect()->route('admin.team-member-roles.index')
            ->with('success', "Role \"{$teamMemberRole->name}\" updated.");
    }

    public function destroy(TeamMemberRole $teamMemberRole)
    {
        if ($teamMemberRole->is_default) {
            return back()->with('error', 'Cannot delete the default role. Set another role as default first.');
        }

        $teamMemberRole->delete();

        return redirect()->route('admin.team-member-roles.index')
            ->with('success', "Role deleted.");
    }
}
