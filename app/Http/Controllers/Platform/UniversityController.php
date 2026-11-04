<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UniversityController extends Controller
{
    private const STATUSES = [
        'pending',
        'trial',
        'active',
        'suspended',
        'expired',
        'cancelled',
    ];

    private function authorizeSuperAdmin(): void
    {
        abort_unless(
            auth()->user()?->hasRole('Super Admin'),
            403,
            'Acesso permitido apenas ao Super Admin.'
        );
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $universities = University::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('slug', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhere('tax_number', 'ILIKE', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('platform.universities.index', compact('universities'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        return view('platform.universities.create');
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:100', 'unique:universities,tax_number'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'string', 'max:255'],
        ]);

        $data['slug'] = $this->generateUniqueSlug($data['name']);
        $data['status'] = 'pending';

        University::create($data);

        return redirect()
            ->route('platform.universities.index')
            ->with('success', 'Universidade registada com sucesso.');
    }

    public function edit(University $university)
    {
        $this->authorizeSuperAdmin();

        return view('platform.universities.edit', compact('university'));
    }

    public function update(Request $request, University $university)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('universities', 'tax_number')->ignore($university->id),
            ],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'string', 'max:255'],
        ]);

        $university->update($data);

        return redirect()
            ->route('platform.universities.index')
            ->with('success', 'Universidade actualizada com sucesso.');
    }

    public function updateStatus(Request $request, University $university)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $status = $data['status'];

        $university->status = $status;

        if (in_array($status, ['active', 'trial'], true)) {
            $university->activated_at ??= now();
            $university->suspended_at = null;
        }

        if ($status === 'suspended') {
            $university->suspended_at = now();
        }

        $university->save();

        return redirect()
            ->route('platform.universities.index')
            ->with('success', 'Estado da universidade actualizado.');
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (University::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
