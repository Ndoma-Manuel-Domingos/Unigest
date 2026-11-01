<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use App\Models\CurriculumSubjectPrerequisite;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CurriculumSubjectController extends Controller
{
    /**
     * Adicionar uma disciplina ao plano curricular.
     */
    public function store(Request $request, Curriculum $curriculum): RedirectResponse
    {
        $data = $request->validate([
            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id'),
            ],
            'year_number' => ['required', 'integer', 'min:1', 'max:10'],
            'semester_number' => ['required', 'integer', 'min:1', 'max:2'],
            'type' => ['required', Rule::in(['obrigatoria', 'opcional', 'electiva'])],
            'credits' => ['required', 'integer', 'min:0'],
            'theoretical_hours' => ['required', 'integer', 'min:0'],
            'practical_hours' => ['required', 'integer', 'min:0'],
            'laboratory_hours' => ['required', 'integer', 'min:0'],
            'order_number' => ['nullable', 'integer', 'min:0'],
        ]);

        $subject = Subject::query()
            ->whereKey($data['subject_id'])
            ->where('is_active', true)
            ->firstOrFail();

        if ($curriculum->subjects()->where('subject_id', $subject->id)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'Esta disciplina já está adicionada ao plano curricular.']);
        }

        $totalHours = $data['theoretical_hours']
            + $data['practical_hours']
            + $data['laboratory_hours'];

        DB::transaction(function () use ($curriculum, $data, $totalHours) {
            $curriculum->subjects()->create([
                'subject_id' => $data['subject_id'],
                'year_number' => $data['year_number'],
                'semester_number' => $data['semester_number'],
                'type' => $data['type'],
                'credits' => $data['credits'],
                'theoretical_hours' => $data['theoretical_hours'],
                'practical_hours' => $data['practical_hours'],
                'laboratory_hours' => $data['laboratory_hours'],
                'total_hours' => $totalHours,
                'order_number' => $data['order_number'] ?? 0,
                'is_active' => true,
            ]);
        });

        return back()->with('success', 'Disciplina adicionada ao plano curricular.');
    }

    /**
     * Actualizar uma disciplina dentro do plano curricular.
     */
    public function update(
        Request $request,
        Curriculum $curriculum,
        CurriculumSubject $curriculumSubject
    ): RedirectResponse {
        $this->ensureCurriculumSubject($curriculum, $curriculumSubject);

        $data = $request->validate([
            'year_number' => ['required', 'integer', 'min:1', 'max:10'],
            'semester_number' => ['required', 'integer', 'min:1', 'max:2'],
            'type' => ['required', Rule::in(['obrigatoria', 'opcional', 'electiva'])],
            'credits' => ['required', 'integer', 'min:0'],
            'theoretical_hours' => ['required', 'integer', 'min:0'],
            'practical_hours' => ['required', 'integer', 'min:0'],
            'laboratory_hours' => ['required', 'integer', 'min:0'],
            'order_number' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['total_hours'] = $data['theoretical_hours']
            + $data['practical_hours']
            + $data['laboratory_hours'];

        $data['order_number'] = $data['order_number'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $curriculumSubject->update($data);

        return back()->with('success', 'Disciplina actualizada no plano curricular.');
    }

    /**
     * Remover uma disciplina do plano curricular.
     * Não apaga a disciplina do catálogo geral.
     */
    public function destroy(
        Curriculum $curriculum,
        CurriculumSubject $curriculumSubject
    ): RedirectResponse {
        $this->ensureCurriculumSubject($curriculum, $curriculumSubject);

        if ($curriculumSubject->requiredBy()->exists()) {
            return back()->withErrors([
                'subject' => 'Não é possível remover esta disciplina. Existem outras disciplinas que a utilizam como pré-requisito.',
            ]);
        }

        DB::transaction(function () use ($curriculumSubject) {
            $curriculumSubject->prerequisites()->delete();
            $curriculumSubject->delete();
        });

        return back()->with('success', 'Disciplina removida do plano curricular.');
    }

    /**
     * Adicionar pré-requisito a uma disciplina.
     */
    public function storePrerequisite(
        Request $request,
        Curriculum $curriculum,
        CurriculumSubject $curriculumSubject
    ): RedirectResponse {
        $this->ensureCurriculumSubject($curriculum, $curriculumSubject);

        $data = $request->validate([
            'prerequisite_curriculum_subject_id' => [
                'required',
                'integer',
                Rule::exists('curriculum_subjects', 'id'),
            ],
        ]);

        $prerequisite = CurriculumSubject::query()
            ->whereKey($data['prerequisite_curriculum_subject_id'])
            ->where('curriculum_id', $curriculum->id)
            ->firstOrFail();

        if ($prerequisite->id === $curriculumSubject->id) {
            return back()->withErrors([
                'prerequisite_curriculum_subject_id' => 'Uma disciplina não pode ser pré-requisito de si própria.',
            ]);
        }

        // O pré-requisito deve estar num ano/semestre anterior.
        $isEarlier = $prerequisite->year_number < $curriculumSubject->year_number
            || (
                $prerequisite->year_number == $curriculumSubject->year_number
                && $prerequisite->semester_number < $curriculumSubject->semester_number
            );

        if (!$isEarlier) {
            return back()->withErrors([
                'prerequisite_curriculum_subject_id' =>
                    'O pré-requisito deve pertencer a um ano ou semestre anterior.',
            ]);
        }

        $alreadyExists = $curriculumSubject->prerequisites()
            ->where('prerequisite_curriculum_subject_id', $prerequisite->id)
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors([
                'prerequisite_curriculum_subject_id' => 'Este pré-requisito já foi associado.',
            ]);
        }

        $curriculumSubject->prerequisites()->create([
            'tenant_id' => $curriculum->tenant_id,
            'prerequisite_curriculum_subject_id' => $prerequisite->id,
        ]);

        return back()->with('success', 'Pré-requisito associado com sucesso.');
    }

    /**
     * Remover um pré-requisito.
     */
    public function destroyPrerequisite(
        Curriculum $curriculum,
        CurriculumSubject $curriculumSubject,
        CurriculumSubjectPrerequisite $prerequisite
    ): RedirectResponse {
        $this->ensureCurriculumSubject($curriculum, $curriculumSubject);

        abort_unless(
            $prerequisite->curriculum_subject_id === $curriculumSubject->id,
            404
        );

        $prerequisite->delete();

        return back()->with('success', 'Pré-requisito removido com sucesso.');
    }

    /**
     * Garantir que a disciplina pertence ao plano curricular recebido.
     */
    private function ensureCurriculumSubject(
        Curriculum $curriculum,
        CurriculumSubject $curriculumSubject
    ): void {
        abort_unless(
            $curriculumSubject->curriculum_id === $curriculum->id,
            404
        );
    }
}
