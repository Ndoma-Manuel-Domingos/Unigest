<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    private function availablePermissions()
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'platform.access')
            ->whereNotIn('name', [
                'universities.view',
                'universities.create',
                'universities.update',
                'universities.delete',
                'universities.activate',
            ])
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => explode('.', $permission->name)[0]);
    }

    private function findRole(int $id): Role
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'Super Admin')
            ->findOrFail($id);
    }

    public function index()
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'Super Admin')
            ->withCount('permissions')
            ->orderBy('name')
            ->paginate(15);

        return view('university.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('university.roles.create', [
            'permissions' => $this->availablePermissions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->where('guard_name', 'web'),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'integer',
                Rule::exists('permissions', 'id')
                    ->where('guard_name', 'web'),
            ],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $permissions = Permission::query()
            ->whereIn('id', $validated['permissions'] ?? [])
            ->where('guard_name', 'web')
            ->where('name', '!=', 'platform.access')
            ->get();

        $role->syncPermissions($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('university.roles.index')
            ->with('success', 'Perfil criado com sucesso.');
    }

    public function edit(int $id)
    {
        $role = $this->findRole($id);

        return view('university.roles.edit', [
            'role' => $role,
            'permissions' => $this->availablePermissions(),
            'selectedPermissions' => $role->permissions->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $role = $this->findRole($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'integer',
                Rule::exists('permissions', 'id')
                    ->where('guard_name', 'web'),
            ],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        $permissions = Permission::query()
            ->whereIn('id', $validated['permissions'] ?? [])
            ->where('guard_name', 'web')
            ->where('name', '!=', 'platform.access')
            ->get();

        $role->syncPermissions($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('university.roles.index')
            ->with('success', 'Perfil actualizado com sucesso.');
    }

    public function destroy(int $id)
    {
        $role = $this->findRole($id);

        abort_if(
            $role->users()->exists(),
            403,
            'Não pode eliminar um perfil que está atribuído a utilizadores.'
        );

        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('university.roles.index')
            ->with('success', 'Perfil eliminado com sucesso.');
    }
}
