<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Plataforma
            'platform.access',
            'universities.view',
            'universities.create',
            'universities.update',
            'universities.delete',
            'universities.activate',

            // Utilizadores
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.activate',

            // Estrutura académica
            'faculties.view',
            'faculties.create',
            'faculties.update',
            'faculties.delete',

            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',

            'courses.view',
            'courses.create',
            'courses.update',
            'courses.delete',

            // Estudantes
            'students.view',
            'students.create',
            'students.update',
            'students.delete',

            // Professores
            'teachers.view',
            'teachers.create',
            'teachers.update',
            'teachers.delete',

            // Matrículas
            'enrollments.view',
            'enrollments.create',
            'enrollments.update',
            'enrollments.cancel',

            //curricula
            'curricula.view',
            'curricula.create',
            'curricula.update',
            'curricula.delete',

            'academic_years.view',
            'academic_years.create',
            'academic_years.update',
            'academic_years.delete',

            'assessments.view',
            'assessments.create',
            'assessments.update',
            'assessments.publish',

            'subject_enrollments.view',
            'subject_enrollments.create',
            'subject_enrollments.update',

            // Actividades académicas
            'subjects.view',
            'subjects.create',
            'subjects.update',
            'subjects.delete',

            'classes.view',
            'classes.manage',

            'grades.view',
            'grades.create',
            'grades.update',

            'attendance.view',
            'attendance.manage',
            'attendance.create',
            'attendance.update',
            'attendance.delete',
            'attendance.register',
            'attendance.reports',

            'rooms.view',
            'rooms.create',
            'rooms.update',
            'rooms.delete',

            'schedules.view',
            'schedules.create',
            'schedules.update',
            'schedules.delete',

            // Financeiro
            'finance.view',
            'finance.manage',
            'payments.view',
            'payments.manage',
            'invoices.view',
            'invoices.manage',

            'finance.fee-concepts.view',
            'finance.fee-concepts.create',
            'finance.fee-concepts.update',
            'finance.fee-concepts.delete',

            'finance.fee-tariffs.view',
            'finance.fee-tariffs.create',
            'finance.fee-tariffs.update',
            'finance.fee-tariffs.delete',


            'finance.invoices.view',
            'finance.invoices.create',
            'finance.invoices.issue',
            'finance.invoices.cancel',

            'finance.payments.view',
            'finance.payments.create',
            'finance.payments.confirm',
            'finance.payments.cancel',
            'finance.payments.reverse',
            'finance.payments.receipt',

            // Caixa e Tesouraria
            'finance.cash.view',
            'finance.cash.open',
            'finance.cash.close',
            'finance.cash.movements.create',
            'finance.cash.reports',

            'finance.suppliers.view',
            'finance.suppliers.create',
            'finance.suppliers.update',
            'finance.suppliers.delete',

            'finance.expense-categories.view',
            'finance.expense-categories.create',
            'finance.expense-categories.update',
            'finance.expense-categories.delete',

            'finance.expenses.view',
            'finance.expenses.create',
            'finance.expenses.update',
            'finance.expenses.approve',
            'finance.expenses.cancel',
            'finance.expenses.payments.create',
            'finance.expenses.payments.view',

            // Relatórios
            'reports.view',
            'reports.export',

            // Biblioteca
            'library.view',
            'library.manage',

            // Documentos
            'documents.view',
            'documents.manage',

            // Configurações
            'settings.view',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $allPermissions = Permission::where(
            'guard_name',
            'web'
        )->get();

        // Super Administrador
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions($allPermissions);

        // Administrador da Universidade
        $universityAdmin = Role::firstOrCreate([
            'name' => 'Administrador da Universidade',
            'guard_name' => 'web',
        ]);

        $universityAdmin->syncPermissions(
            $allPermissions->reject(
                fn ($permission) => $permission->name === 'platform.access'
                    || str_starts_with($permission->name, 'universities.')
            )
        );

        // Reitor
        $this->createRole('Reitor', [
            'faculties.view',
            'departments.view',
            'courses.view',
            'students.view',
            'teachers.view',
            'enrollments.view',
            'subjects.view',
            'classes.view',
            'grades.view',
            'attendance.view',
            'finance.view',
            'payments.view',
            'invoices.view',
            'reports.view',
            'reports.export',
            'library.view',
            'documents.view',
            'settings.view',
        ]);

        // Vice-Reitor
        $this->createRole('Vice-Reitor', [
            'faculties.view',
            'departments.view',
            'courses.view',
            'students.view',
            'teachers.view',
            'enrollments.view',
            'subjects.view',
            'classes.view',
            'grades.view',
            'attendance.view',
            'reports.view',
            'documents.view',
        ]);

        // Director
        $this->createRole('Director', [
            'departments.view',
            'departments.update',
            'courses.view',
            'courses.create',
            'courses.update',
            'students.view',
            'teachers.view',
            'enrollments.view',
            'subjects.view',
            'subjects.create',
            'subjects.update',
            'classes.view',
            'classes.manage',
            'grades.view',
            'attendance.view',
            'attendance.manage',
            'reports.view',
            'documents.view',
            'documents.manage',
        ]);

        // Chefe de Departamento
        $this->createRole('Chefe de Departamento', [
            'courses.view',
            'students.view',
            'teachers.view',
            'subjects.view',
            'subjects.create',
            'subjects.update',
            'classes.view',
            'classes.manage',
            'grades.view',
            'grades.create',
            'grades.update',
            'attendance.view',
            'attendance.manage',
            'reports.view',
            'documents.view',
        ]);

        // Professor
        $this->createRole('Professor', [
            'students.view',
            'subjects.view',
            'classes.view',
            'grades.view',
            'grades.create',
            'grades.update',
            'attendance.view',
            'attendance.manage',
            'documents.view',
        ]);

        // Funcionário
        $this->createRole('Funcionário', [
            'students.view',
            'students.create',
            'students.update',
            'enrollments.view',
            'enrollments.create',
            'enrollments.update',
            'finance.view',
            'payments.view',
            'payments.manage',
            'invoices.view',
            'invoices.manage',
            'documents.view',
            'documents.manage',
            'reports.view',
        ]);

        // Estudante
        $this->createRole('Estudante', [
            'subjects.view',
            'classes.view',
            'grades.view',
            'attendance.view',
            'documents.view',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function createRole(string $name, array $permissions): void
    {
        $role = Role::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($permissions);
    }
}
