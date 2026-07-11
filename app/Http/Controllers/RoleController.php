<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected array $systemRoles = ['Admin', 'User'];

    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('dashboard.admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return str_contains($p->name, 'manage-') || $p->name === 'access-admin-panel'
                ? 'Admin Capabilities'
                : 'User Capabilities';
        });
        return view('dashboard.admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->filled('permissions')) {
            $permNames = Permission::whereIn('id', $request->permissions)->pluck('name');
            $role->syncPermissions($permNames);
        }

        Cache::forget('admin_roles');
        Cache::forget('admin_permissions');

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" created.");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return str_contains($p->name, 'manage-') || $p->name === 'access-admin-panel'
                ? 'Admin Capabilities'
                : 'User Capabilities';
        });
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();
        return view('dashboard.admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        if (!in_array($role->name, $this->systemRoles)) {
            $role->update(['name' => $request->name]);
        }

        $permNames = $request->filled('permissions')
            ? Permission::whereIn('id', $request->permissions)->pluck('name')
            : [];

        $role->syncPermissions($permNames);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Cache::forget('admin_roles');
        Cache::forget('admin_permissions');

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, $this->systemRoles)) {
            return back()->with('error', "The \"{$role->name}\" role is a system role and cannot be deleted.");
        }

        $role->delete();
        Cache::forget('admin_roles');
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }
}
