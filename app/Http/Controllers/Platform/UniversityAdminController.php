<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UniversityAdminController extends Controller
{
    private const ADMIN_ROLE = 'Administrador da Universidade';

    private function authorizeSuperAdmin(): void
    {
        abort_unless(
            auth()->user()?->hasRole('Super Admin'),
            403,
            'Acesso permitido apenas ao Super Admin.'
        );
    }

    private function getUniversityAdmin(
        University $university,
        int $userId
    ): User {
        return User::query()
            ->where('tenant_id', $university->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', self::ADMIN_ROLE)
                    ->where('guard_name', 'web');
            })
            ->findOrFail($userId);
    }

    public function index(University $university)
    {
        $this->authorizeSuperAdmin();

        $administrators = User::query()
            ->where('tenant_id', $university->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', self::ADMIN_ROLE)
                    ->where('guard_name', 'web');
            })
            ->latest()
            ->paginate(10);

        return view(
            'platform.universities.administrators.index',
            compact('university', 'administrators')
        );
    }

    public function create(University $university)
    {
        $this->authorizeSuperAdmin();

        return view(
            'platform.universities.administrators.create',
            compact('university')
        );
    }

    public function store(Request $request, University $university)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $role = Role::firstOrCreate([
            'name' => self::ADMIN_ROLE,
            'guard_name' => 'web',
        ]);

        $administrator = new User();

        $administrator->name = $data['name'];
        $administrator->email = $data['email'];
        $administrator->password = Hash::make($data['password']);
        $administrator->tenant_id = $university->id;
        $administrator->is_active = true;
        $administrator->email_verified_at = now();

        if (isset($data['phone'])) {
            $administrator->phone = $data['phone'];
        }

        $administrator->save();

        $administrator->assignRole($role);

        return redirect()
            ->route('platform.universities.administrators.index', $university)
            ->with('success', 'Administrador criado com sucesso.');
    }

    public function updateStatus(
        Request $request,
        University $university,
        int $userId
    ) {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $administrator = $this->getUniversityAdmin(
            $university,
            $userId
        );

        $administrator->is_active = $data['is_active'];
        $administrator->save();

        return back()->with(
            'success',
            'Estado do administrador actualizado com sucesso.'
        );
    }
}
