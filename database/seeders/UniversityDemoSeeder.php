<?php

namespace Database\Seeders;

use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\AssessmentGrade;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectAssessment;
use App\Models\SubjectEnrollment;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

use App\Tenancy\TenantContext;

class UniversityDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            /*
            |--------------------------------------------------------------------------
            | 1. UNIVERSIDADE
            |--------------------------------------------------------------------------
            */

            $university = University::updateOrCreate(
                [
                    'tax_number' => '999999999',
                ],
                [
                    'name' => 'Universidade de Demonstração de Luanda',
                    'short_name' => 'UDL',
                    'slug' => 'universidade-demonstracao-luanda',
                    'email' => 'admin@udl.demo.ao',
                    'phone' => '+244 923 000 001',
                    'website' => 'https://udl.demo.ao',
                    'address' => 'Talatona',
                    'city' => 'Luanda',
                    'province' => 'Luanda',
                    'country' => 'Angola',
                    'status' => 'active',
                    'activated_at' => now(),
                ]
            );

            $tenantContext = app(TenantContext::class);

            $tenantContext->set($university);

            /*
            |--------------------------------------------------------------------------
            | 2. ADMINISTRADOR DA UNIVERSIDADE
            |--------------------------------------------------------------------------
            */

            $admin = User::updateOrCreate(
                [
                    'email' => 'admin@udl.demo.ao',
                ],
                [
                    'name' => 'Administrador da UDL',
                    'password' => Hash::make('Admin@123456'),
                    'tenant_id' => $university->id,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            $adminRole = Role::where('name', 'Administrador da Universidade')
                ->where('guard_name', 'web')
                ->first();

            if ($adminRole) {
                $admin->syncRoles([$adminRole]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3. FACULDADES
            |--------------------------------------------------------------------------
            */

            $faculdadeTecnologia = Faculty::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'name' => 'Faculdade de Engenharia e Tecnologias',
                ],
                [
                    'acronym' => 'FET',
                    'description' => 'Faculdade dedicada às áreas de engenharia, informática e tecnologias.',
                    'is_active' => true,
                ]
            );

            $faculdadeEconomia = Faculty::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'name' => 'Faculdade de Economia e Gestão',
                ],
                [
                    'acronym' => 'FEG',
                    'description' => 'Faculdade dedicada às áreas de economia, gestão e administração.',
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 4. DEPARTAMENTOS
            |--------------------------------------------------------------------------
            */

            $departamentoInformatica = Department::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'faculty_id' => $faculdadeTecnologia->id,
                    'name' => 'Departamento de Informática',
                ],
                [
                    'acronym' => 'DI',
                    'description' => 'Departamento de Informática e Sistemas de Informação.',
                    'is_active' => true,
                ]
            );

            $departamentoEngenharia = Department::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'faculty_id' => $faculdadeTecnologia->id,
                    'name' => 'Departamento de Engenharia',
                ],
                [
                    'acronym' => 'DE',
                    'description' => 'Departamento de Engenharia.',
                    'is_active' => true,
                ]
            );

            $departamentoGestao = Department::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'faculty_id' => $faculdadeEconomia->id,
                    'name' => 'Departamento de Gestão',
                ],
                [
                    'acronym' => 'DG',
                    'description' => 'Departamento de Gestão e Administração.',
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 5. CURSOS
            |--------------------------------------------------------------------------
            */

            $cursoInformatica = Course::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'code' => 'ENG-IT',
                ],
                [
                    'faculty_id' => $faculdadeTecnologia->id,
                    'department_id' => $departamentoInformatica->id,
                    'name' => 'Engenharia Informática',
                    'acronym' => 'EI',
                    'degree_type' => 'licenciatura',
                    'duration_years' => 4,
                    'duration_semesters' => 8,
                    'study_regime' => 'presencial',
                    'is_active' => true,
                ]
            );

            $cursoGestao = Course::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'code' => 'GAC',
                ],
                [
                    'faculty_id' => $faculdadeEconomia->id,
                    'department_id' => $departamentoGestao->id,
                    'name' => 'Gestão e Administração',
                    'acronym' => 'GA',
                    'degree_type' => 'licenciatura',
                    'duration_years' => 4,
                    'duration_semesters' => 8,
                    'study_regime' => 'presencial',
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 6. DISCIPLINAS
            |--------------------------------------------------------------------------
            */

            $disciplinas = [];

            $disciplinas[] = Subject::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'code' => 'ALG1',
                ],
                [
                    'department_id' => $departamentoInformatica->id,
                    'name' => 'Álgebra Linear',
                    'type' => 'obrigatoria',
                    'year_number' => 1,
                    'semester_number' => 1,
                    'credits' => 6,
                    'theoretical_hours' => 45,
                    'practical_hours' => 15,
                    'laboratory_hours' => 0,
                    'total_hours' => 60,
                    'requires_prerequisite' => false,
                    'is_active' => true,
                ]
            );

            $disciplinas[] = Subject::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'code' => 'PROG1',
                ],
                [
                    'department_id' => $departamentoInformatica->id,
                    'name' => 'Programação I',
                    'type' => 'obrigatoria',
                    'year_number' => 1,
                    'semester_number' => 1,
                    'credits' => 6,
                    'theoretical_hours' => 30,
                    'practical_hours' => 30,
                    'laboratory_hours' => 15,
                    'total_hours' => 75,
                    'requires_prerequisite' => false,
                    'is_active' => true,
                ]
            );

            $disciplinas[] = Subject::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'code' => 'BD1',
                ],
                [
                    'department_id' => $departamentoInformatica->id,
                    'name' => 'Bases de Dados',
                    'type' => 'obrigatoria',
                    'year_number' => 2,
                    'semester_number' => 1,
                    'credits' => 6,
                    'theoretical_hours' => 30,
                    'practical_hours' => 30,
                    'laboratory_hours' => 15,
                    'total_hours' => 75,
                    'requires_prerequisite' => true,
                    'is_active' => true,
                ]
            );

            $disciplinas[] = Subject::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'code' => 'POO1',
                ],
                [
                    'department_id' => $departamentoInformatica->id,
                    'name' => 'Programação Orientada a Objectos',
                    'type' => 'obrigatoria',
                    'year_number' => 2,
                    'semester_number' => 1,
                    'credits' => 6,
                    'theoretical_hours' => 30,
                    'practical_hours' => 30,
                    'laboratory_hours' => 15,
                    'total_hours' => 75,
                    'requires_prerequisite' => true,
                    'is_active' => true,
                ]
            );

            $disciplinaGestao = Subject::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'code' => 'GES1',
                ],
                [
                    'department_id' => $departamentoGestao->id,
                    'name' => 'Fundamentos de Gestão',
                    'type' => 'obrigatoria',
                    'year_number' => 1,
                    'semester_number' => 1,
                    'credits' => 6,
                    'theoretical_hours' => 45,
                    'practical_hours' => 15,
                    'laboratory_hours' => 0,
                    'total_hours' => 60,
                    'requires_prerequisite' => false,
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 7. PLANOS CURRICULARES
            |--------------------------------------------------------------------------
            */

            $curriculum = Curriculum::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'course_id' => $cursoInformatica->id,
                    'name' => 'Plano Curricular 2026',
                ],
                [
                    'version' => '1.0',
                    'effective_from' => '2026-01-01',
                    'description' => 'Plano curricular de demonstração da Engenharia Informática.',
                    'is_active' => true,
                ]
            );

            $curriculumSubjects = [];

            foreach ([
                [$disciplinas[0], 1, 1, 'obrigatoria'],
                [$disciplinas[1], 1, 1, 'obrigatoria'],
                [$disciplinas[2], 2, 1, 'obrigatoria'],
                [$disciplinas[3], 2, 1, 'obrigatoria'],
            ] as [$subject, $year, $semester, $type]) {
                $curriculumSubjects[$subject->code] =
                    CurriculumSubject::updateOrCreate(
                        [
                            'tenant_id' => $university->id,
                            'curriculum_id' => $curriculum->id,
                            'subject_id' => $subject->id,
                        ],
                        [
                            'year_number' => $year,
                            'semester_number' => $semester,
                            'type' => $type,
                            'credits' => $subject->credits,
                            'total_hours' => $subject->total_hours,
                            'order_number' => $subject->id,
                            'is_active' => true,
                        ]
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | 8. ANO LECTIVO
            |--------------------------------------------------------------------------
            */

            $academicYear = AcademicYear::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'name' => '2026/2027',
                ],
                [
                    'start_date' => '2026-09-01',
                    'end_date' => '2027-07-31',
                    'is_current' => true,
                    'is_active' => true,
                    'description' => 'Ano lectivo 2026/2027.',
                ]
            );

            AcademicYear::where('tenant_id', $university->id)
                ->where('id', '!=', $academicYear->id)
                ->update([
                    'is_current' => false,
                ]);

            /*
            |--------------------------------------------------------------------------
            | 9. TURMA
            |--------------------------------------------------------------------------
            */

            $academicClass = AcademicClass::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'code' => 'EI-2026-T1',
                ],
                [
                    'academic_year_id' => $academicYear->id,
                    'course_id' => $cursoInformatica->id,
                    'curriculum_id' => $curriculum->id,
                    'name' => 'Engenharia Informática - 1.º Ano - Turma A',
                    'year_number' => 1,
                    'semester_number' => 1,
                    'shift' => 'manha',
                    'study_regime' => 'presencial',
                    'capacity' => 40,
                    'start_date' => '2026-09-01',
                    'end_date' => '2027-07-31',
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 10. ESTUDANTES
            |--------------------------------------------------------------------------
            */

            $students = [];

            $studentData = [
                [
                    'student_number' => 'EST-2026-000001',
                    'name' => 'João Manuel Silva',
                    'gender' => 'M',
                    'birth_date' => '2005-03-12',
                    'email' => 'joao.silva@udl.demo.ao',
                    'phone' => '+244 923 100 001',
                ],
                [
                    'student_number' => 'EST-2026-000002',
                    'name' => 'Maria Isabel Domingos',
                    'gender' => 'F',
                    'birth_date' => '2004-08-20',
                    'email' => 'maria.domingos@udl.demo.ao',
                    'phone' => '+244 923 100 002',
                ],
                [
                    'student_number' => 'EST-2026-000003',
                    'name' => 'Pedro António Manuel',
                    'gender' => 'M',
                    'birth_date' => '2005-01-15',
                    'email' => 'pedro.manuel@udl.demo.ao',
                    'phone' => '+244 923 100 003',
                ],
                [
                    'student_number' => 'EST-2026-000004',
                    'name' => 'Ana Paula Francisco',
                    'gender' => 'F',
                    'birth_date' => '2004-11-02',
                    'email' => 'ana.francisco@udl.demo.ao',
                    'phone' => '+244 923 100 004',
                ],
                [
                    'student_number' => 'EST-2026-000005',
                    'name' => 'Carlos Alberto José',
                    'gender' => 'M',
                    'birth_date' => '2005-06-25',
                    'email' => 'carlos.jose@udl.demo.ao',
                    'phone' => '+244 923 100 005',
                ],
            ];

            foreach ($studentData as $data) {
                $students[] = Student::updateOrCreate(
                    [
                        'tenant_id' => $university->id,
                        'student_number' => $data['student_number'],
                    ],
                    array_merge($data, [
                        'nif' => 'NIF-' . $data['student_number'],
                        'document_type' => 'bilhete_identidade',
                        'document_number' => 'BI-' . $data['student_number'],
                        'nationality' => 'Angolana',
                        'province' => 'Luanda',
                        'municipality' => 'Luanda',
                        'is_active' => true,
                    ])
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 11. MATRÍCULAS
            |--------------------------------------------------------------------------
            */

            $enrollments = [];

            foreach ($students as $index => $student) {
                $enrollments[] = Enrollment::updateOrCreate(
                    [
                        'tenant_id' => $university->id,
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'course_id' => $cursoInformatica->id,
                        'academic_class_id' => $academicClass->id,
                        'curriculum_id' => $curriculum->id,
                        'enrollment_number' => 'MAT-2026-' . str_pad(
                            $index + 1,
                            6,
                            '0',
                            STR_PAD_LEFT
                        ),
                        'enrollment_date' => '2026-09-01',
                        'year_number' => 1,
                        'semester_number' => 1,
                        'enrollment_type' => 'regular',
                        'status' => 'active',
                        'registered_by' => $admin->id,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 12. INSCRIÇÃO NAS DISCIPLINAS
            |--------------------------------------------------------------------------
            */

            foreach ($enrollments as $enrollment) {
                foreach ([
                    $curriculumSubjects['ALG1'],
                    $curriculumSubjects['PROG1'],
                ] as $curriculumSubject) {
                    SubjectEnrollment::updateOrCreate(
                        [
                            'tenant_id' => $university->id,
                            'enrollment_id' => $enrollment->id,
                            'curriculum_subject_id' => $curriculumSubject->id,
                        ],
                        [
                            'enrollment_date' => '2026-09-02',
                            'status' => 'enrolled',
                            'registered_by' => $admin->id,
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 13. AVALIAÇÕES
            |--------------------------------------------------------------------------
            */

            $assessment = SubjectAssessment::updateOrCreate(
                [
                    'tenant_id' => $university->id,
                    'academic_class_id' => $academicClass->id,
                    'curriculum_subject_id' => $curriculumSubjects['PROG1']->id,
                    'title' => 'Primeiro Teste de Programação I',
                ],
                [
                    'type' => 'test',
                    'assessment_date' => '2026-10-20',
                    'weight' => 40,
                    'max_grade' => 20,
                    'description' => 'Primeiro teste de avaliação.',
                    'is_published' => false,
                    'created_by' => $admin->id,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 14. NOTAS
            |--------------------------------------------------------------------------
            */

            foreach ($enrollments as $index => $enrollment) {
                $subjectEnrollment = SubjectEnrollment::where(
                    'tenant_id',
                    $university->id
                )
                    ->where('enrollment_id', $enrollment->id)
                    ->where(
                        'curriculum_subject_id',
                        $curriculumSubjects['PROG1']->id
                    )
                    ->first();

                if (!$subjectEnrollment) {
                    continue;
                }

                AssessmentGrade::updateOrCreate(
                    [
                        'tenant_id' => $university->id,
                        'subject_assessment_id' => $assessment->id,
                        'subject_enrollment_id' => $subjectEnrollment->id,
                    ],
                    [
                        'grade' => [14, 16, 11, 9, 13][$index] ?? 10,
                        'observations' => null,
                        'entered_by' => $admin->id,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 15. SESSÕES DE AULA
            |--------------------------------------------------------------------------
            */

            $sessions = [];

            $sessionDates = [
                '2026-10-05',
                '2026-10-07',
                '2026-10-12',
                '2026-10-14',
            ];

            foreach ($sessionDates as $date) {
                $sessions[] = ClassSession::updateOrCreate(
                    [
                        'tenant_id' => $university->id,
                        'academic_class_id' => $academicClass->id,
                        'curriculum_subject_id' => $curriculumSubjects['PROG1']->id,
                        'session_date' => $date,
                    ],
                    [
                        'start_time' => '08:00',
                        'end_time' => '10:00',
                        'topic' => 'Aula de Programação I',
                        'status' => 'completed',
                        'recorded_by' => $admin->id,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 16. PRESENÇAS
            |--------------------------------------------------------------------------
            */

            foreach ($sessions as $sessionIndex => $session) {
                foreach ($enrollments as $studentIndex => $enrollment) {
                    $subjectEnrollment = SubjectEnrollment::where(
                        'tenant_id',
                        $university->id
                    )
                        ->where('enrollment_id', $enrollment->id)
                        ->where(
                            'curriculum_subject_id',
                            $curriculumSubjects['PROG1']->id
                        )
                        ->first();

                    if (!$subjectEnrollment) {
                        continue;
                    }

                    $status = 'present';

                    if ($sessionIndex === 1 && $studentIndex === 1) {
                        $status = 'late';
                    }

                    if ($sessionIndex === 2 && $studentIndex === 3) {
                        $status = 'absent';
                    }

                    if ($sessionIndex === 3 && $studentIndex === 4) {
                        $status = 'excused';
                    }

                    AttendanceRecord::updateOrCreate(
                        [
                            'tenant_id' => $university->id,
                            'class_session_id' => $session->id,
                            'subject_enrollment_id' => $subjectEnrollment->id,
                        ],
                        [
                            'status' => $status,
                            'arrival_time' => $status === 'late'
                                ? '08:25'
                                : null,
                            'justification' => $status === 'excused' ? 'Ausência justificada.' : null,
                            'recorded_by' => $admin->id,
                        ]
                    );
                }
            }
        });

        $this->command->info('');
        $this->command->info('==============================================');
        $this->command->info(' UNIVERSIDADE DEMO CRIADA COM SUCESSO');
        $this->command->info('==============================================');
        $this->command->info('');
        $this->command->info('Universidade: Universidade de Demonstração de Luanda');
        $this->command->info('Código: UDL');
        $this->command->info('');
        $this->command->info('Administrador: admin@udl.demo.ao');
        $this->command->info('Password: Admin@123456');
        $this->command->info('');
        $this->command->info('Ano lectivo: 2026/2027');
        $this->command->info('Curso: Engenharia Informática');
        $this->command->info('Turma: EI-2026-T1');
        $this->command->info('Estudantes: 5');
        $this->command->info('');
        $this->command->info('==============================================');
    }
}
