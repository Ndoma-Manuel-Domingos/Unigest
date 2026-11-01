<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Matrícula {{ $enrollment->enrollment_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            margin: 35px;
            font-size: 13px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 15px;
            background: #eee;
            padding: 9px;
            margin-top: 25px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .field {
            margin-bottom: 12px;
        }

        .label {
            color: #666;
            font-size: 11px;
        }

        .value {
            font-weight: bold;
            margin-top: 4px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 75px;
        }

        .signature {
            width: 40%;
            text-align: center;
            border-top: 1px solid #222;
            padding-top: 8px;
        }

        .actions {
            margin-bottom: 20px;
        }

        @media print {
            .actions {
                display: none;
            }

            body {
                margin: 15mm;
            }
        }

    </style>
</head>
<body>
    <div class="actions">
        <button onclick="window.print()">Imprimir ficha</button>
    </div>

    <div class="header">
        <h1>{{ auth()->user()->university->name ?? 'Instituição de Ensino Superior' }}</h1>
        <p>FICHA DE MATRÍCULA</p>
        <h2>{{ $enrollment->enrollment_number }}</h2>
    </div>

    <h2>1. Dados do estudante</h2>
    <div class="grid">
        <div class="field">
            <div class="label">Nome completo</div>
            <div class="value">{{ $enrollment->student->name }}</div>
        </div>
        <div class="field">
            <div class="label">Número de estudante</div>
            <div class="value">{{ $enrollment->student->student_number }}</div>
        </div>
        <div class="field">
            <div class="label">NIF</div>
            <div class="value">{{ $enrollment->student->nif ?: '—' }}</div>
        </div>
        <div class="field">
            <div class="label">Telefone</div>
            <div class="value">{{ $enrollment->student->phone ?: '—' }}</div>
        </div>
        <div class="field">
            <div class="label">E-mail</div>
            <div class="value">{{ $enrollment->student->email ?: '—' }}</div>
        </div>
        <div class="field">
            <div class="label">Data de nascimento</div>
            <div class="value">
                {{ $enrollment->student->birth_date?->format('d/m/Y') ?? '—' }}
            </div>
        </div>
    </div>

    <h2>2. Dados académicos</h2>
    <div class="grid">
        <div class="field">
            <div class="label">Ano académico</div>
            <div class="value">{{ $enrollment->academicYear->name }}</div>
        </div>
        <div class="field">
            <div class="label">Curso</div>
            <div class="value">{{ $enrollment->course->name }}</div>
        </div>
        <div class="field">
            <div class="label">Turma</div>
            <div class="value">{{ $enrollment->academicClass->name }}</div>
        </div>
        <div class="field">
            <div class="label">Plano curricular</div>
            <div class="value">{{ $enrollment->curriculum->name ?? '—' }}</div>
        </div>
        <div class="field">
            <div class="label">Ano curricular</div>
            <div class="value">{{ $enrollment->year_number }}.º ano</div>
        </div>
        <div class="field">
            <div class="label">Semestre</div>
            <div class="value">
                {{ $enrollment->semester_number
                    ? $enrollment->semester_number . '.º semestre'
                    : '—' }}
            </div>
        </div>
        <div class="field">
            <div class="label">Data da matrícula</div>
            <div class="value">{{ $enrollment->enrollment_date?->format('d/m/Y') }}</div>
        </div>
        <div class="field">
            <div class="label">Estado</div>
            <div class="value">{{ ucfirst($enrollment->status) }}</div>
        </div>
    </div>

    @if($enrollment->observations)
    <h2>3. Observações</h2>
    <p>{{ $enrollment->observations }}</p>
    @endif

    <div class="signatures">
        <div class="signature">Assinatura do estudante</div>
        <div class="signature">Assinatura e carimbo da instituição</div>
    </div>

    <p style="text-align:center; margin-top:40px; color:#666;">
        Documento emitido em {{ now()->format('d/m/Y H:i') }}
    </p>
</body>
</html>
