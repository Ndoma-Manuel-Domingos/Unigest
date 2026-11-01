<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_subject_prerequisites', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id');

            $table->foreignId('curriculum_subject_id');

            $table->foreignId('prerequisite_curriculum_subject_id');

            $table->timestamps();

            // Chave estrangeira: universidade
            $table->foreign('tenant_id', 'csp_tenant_fk')
                ->references('id')
                ->on('universities')
                ->cascadeOnDelete();

            // Chave estrangeira: disciplina
            $table->foreign('curriculum_subject_id', 'csp_subject_fk')
                ->references('id')
                ->on('curriculum_subjects')
                ->cascadeOnDelete();

            // Chave estrangeira: disciplina pré-requisito
            $table->foreign('prerequisite_curriculum_subject_id', 'csp_prerequisite_fk')
                ->references('id')
                ->on('curriculum_subjects')
                ->cascadeOnDelete();

            // Impedir pré-requisitos duplicados
            $table->unique(
                [
                    'curriculum_subject_id',
                    'prerequisite_curriculum_subject_id',
                ],
                'csp_subject_prereq_unique'
            );

            // Índice para consultas por universidade e disciplina
            $table->index(
                ['tenant_id', 'curriculum_subject_id'],
                'csp_tenant_subject_idx'
            );

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_subject_prerequisites');
    }
};
