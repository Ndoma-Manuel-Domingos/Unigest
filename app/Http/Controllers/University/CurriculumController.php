<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Subject;
use App\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function __construct(
        private TenantContext $tenantContext
    ) {
    }

    public function index(Request $request): View
    {
        $tenantId = $this->tenantContext->id();

        $curricula = Curriculum::query()
            ->with('course')
            ->withCount('subjects')
            ->where('tenant_id', $tenantId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('version', 'ILIKE', "%{$search}%")
                        ->orWhereHas('course', function ($courseQuery) use ($search) {
                            $courseQuery
                                ->where('name', 'ILIKE', "%{$search}%")
                                ->orWhere('code', 'ILIKE', "%{$search}%");
                        });
                });
            })
            ->when(
                $request->filled('course_id'),
                fn ($query) => $query->where('course_id', $request->integer('course_id'))
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'is_active',
                    $request->status === 'active'
                )
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $courses = Course::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('university.curricula.index', [
            'curricula' => $curricula,
            'courses' => $courses,
        ]);
    }

    public function create(): View
    {
        $courses = Course::query()
            ->where('tenant_id', $this->tenantContext->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('university.curricula.create', [
            'courses' => $courses,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $course = Course::query()
            ->where('tenant_id', $this->tenantContext->id())
            ->whereKey($data['course_id'])
            ->firstOrFail();

        $curriculum = Curriculum::create([
            'tenant_id' => $this->tenantContext->id(),
            'course_id' => $course->id,
            'name' => $data['name'],
            'version' => $data['version'] ?? null,
            'effective_from' => $data['effective_from'] ?? null,
            'effective_to' => $data['effective_to'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('university.curricula.edit', $curriculum)
            ->with('success', 'Currículo criado com sucesso.');
    }

    public function edit(string $id): View
    {
        $curriculum = Curriculum::findOrFail($id);

        $this->ensureTenant($curriculum);

        $curriculum->load([
            'course',
            'subjects' => fn ($query) => $query
                ->with([
                    'subject',
                    'prerequisites.prerequisiteSubject.subject',
                ])
                ->orderBy('year_number')
                ->orderBy('semester_number')
                ->orderBy('order_number'),
        ]);

        $courses = Course::query()
            ->where('tenant_id', $this->tenantContext->id())
            ->orderBy('name')
            ->get();

        $availableSubjects = Subject::query()
            ->where('tenant_id', $this->tenantContext->id())
            ->where('is_active', true)
            ->whereDoesntHave('curriculumSubjects', function ($query) use ($curriculum) {
                $query->where('curriculum_id', $curriculum->id);
            })
            ->orderBy('name')
            ->get();

        return view('university.curricula.edit', [
            'curriculum' => $curriculum,
            'courses' => $courses,
            'availableSubjects' => $availableSubjects,
        ]);
    }

    public function update(
        Request $request,
        string $id
    ): RedirectResponse {

        $curriculum = Curriculum::findOrFail($id);

        $this->ensureTenant($curriculum);

        $data = $this->validateData($request, $curriculum);

        Course::query()
            ->where('tenant_id', $this->tenantContext->id())
            ->whereKey($data['course_id'])
            ->firstOrFail();

        $curriculum->update([
            'course_id' => $data['course_id'],
            'name' => $data['name'],
            'version' => $data['version'] ?? null,
            'effective_from' => $data['effective_from'] ?? null,
            'effective_to' => $data['effective_to'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('university.curricula.edit', $curriculum)
            ->with('success', 'Currículo actualizado com sucesso.');
    }

    public function updateStatus(
        Request $request,
        Curriculum $curriculum
    ): RedirectResponse {
        $this->ensureTenant($curriculum);

        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $curriculum->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with(
            'success',
            $curriculum->is_active
                ? 'Currículo activado com sucesso.'
                : 'Currículo desactivado com sucesso.'
        );
    }

    public function destroy(Curriculum $curriculum): RedirectResponse
    {
        $this->ensureTenant($curriculum);

        if ($curriculum->subjects()->exists()) {
            return back()->withErrors([
                'curriculum' => 'Não é possível eliminar um currículo que possui disciplinas associadas.',
            ]);
        }

        $curriculum->delete();

        return redirect()
            ->route('university.curricula.index')
            ->with('success', 'Currículo eliminado com sucesso.');
    }

    private function validateData(
        Request $request,
        ?Curriculum $curriculum = null
    ): array {
        return $request->validate([
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id')->where(
                    fn ($query) => $query->where(
                        'tenant_id',
                        $this->tenantContext->id()
                    )
                ),
            ],

            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('curricula', 'name')
                    ->where(
                        fn ($query) => $query
                            ->where('tenant_id', $this->tenantContext->id())
                            ->where('course_id', $request->integer('course_id'))
                    )
                    ->ignore($curriculum?->id),
            ],

            'version' => [
                'nullable',
                'string',
                'max:50',
            ],

            'effective_from' => [
                'nullable',
                'date',
            ],

            'effective_to' => [
                'nullable',
                'date',
                'after_or_equal:effective_from',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);
    }

    private function ensureTenant(Curriculum $curriculum): void
    {
        abort_unless(
            $curriculum->tenant_id === $this->tenantContext->id(),
            404
        );
    }
}
