<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private TenantContext $tenantContext
    ) {
    }

    private function universityId(): int
    {
        $id = $this->tenantContext->id();

        abort_unless($id, 403, 'Universidade não identificada.');

        return $id;
    }

    private function findUser(int $userId): User
    {
        return User::query()
            ->where('tenant_id', $this->universityId())
            ->findOrFail($userId);
    }

    private function availableRoles()
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'Super Admin')
            ->orderBy('name')
            ->get();
    }

    public function index(Request $request)
    {
        $query = User::query()
            ->with('roles')
            ->where('tenant_id', $this->universityId());

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('university.users.index', [
            'users' => $users,
            'roles' => $this->availableRoles(),
        ]);
    }

    public function create()
    {
        return view('university.users.create', [
            'roles' => $this->availableRoles(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => [
                'required',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web')
                    ->where('name', '!=', 'Super Admin'),
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'tenant_id' => $this->universityId(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $user->assignRole($validated['role']);
        });

        return redirect()
            ->route('university.users.index')
            ->with('success', 'Utilizador criado com sucesso.');
    }

    public function edit(int $userId)
    {
        $user = $this->findUser($userId);

        return view('university.users.edit', [
            'user' => $user,
            'roles' => $this->availableRoles(),
        ]);
    }

    public function update(Request $request, int $userId)
    {
        $user = $this->findUser($userId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => [
                'required',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web')
                    ->where('name', '!=', 'Super Admin'),
            ],
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            $user->syncRoles([$validated['role']]);
        });

        return redirect()
            ->route('university.users.index')
            ->with('success', 'Utilizador actualizado com sucesso.');
    }

    public function updateStatus(int $userId)
    {
        $user = $this->findUser($userId);

        abort_if(
            $user->id === auth()->id(),
            403,
            'Não pode desactivar a sua própria conta.'
        );

        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()
            ->route('university.users.index')
            ->with('success', 'Estado do utilizador actualizado.');
    }

    public function destroy(int $userId)
    {
        $user = $this->findUser($userId);

        abort_if(
            $user->id === auth()->id(),
            403,
            'Não pode eliminar a sua própria conta.'
        );

        abort_if(
            $user->hasRole('Super Admin'),
            403,
            'Não pode eliminar um Super Admin.'
        );

        $user->delete();

        return redirect()
            ->route('university.users.index')
            ->with('success', 'Utilizador eliminado com sucesso.');
    }
}
