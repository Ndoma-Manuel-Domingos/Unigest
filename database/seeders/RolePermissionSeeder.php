<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Administrador da Universidade',
            'Reitor',
            'Vice-Reitor',
            'Director',
            'Chefe de Departamento',
            'Funcionário Administrativo',
            'Funcionário Financeiro',
            'Secretário Académico',
            'Professor',
            'Estudante',
            'Bibliotecário',
            'Técnico de Informática',
            'Recursos Humanos',
            'Responsável de Património',
            'Auditor',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }
}