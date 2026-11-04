<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Middleware
use App\Http\Middleware\EnsureTenant;

// Controllers da plataforma
use App\Http\Controllers\Platform\UniversityController;
use App\Http\Controllers\Platform\UniversityAdminController;

// Controllers da universidade
use App\Http\Controllers\University\UserController;
use App\Http\Controllers\University\RoleController;
use App\Http\Controllers\University\FacultyController;
use App\Http\Controllers\University\DepartmentController;
use App\Http\Controllers\University\CourseController;
use App\Http\Controllers\University\SubjectController;
use App\Http\Controllers\University\CurriculumController;
use App\Http\Controllers\University\CurriculumSubjectController;
use App\Http\Controllers\University\AcademicYearController;
use App\Http\Controllers\University\StudentController;
use App\Http\Controllers\University\AcademicClassController;
use App\Http\Controllers\University\EnrollmentController;
use App\Http\Controllers\University\SubjectEnrollmentController;
use App\Http\Controllers\University\SubjectAssessmentController;
use App\Http\Controllers\University\AttendanceController;
use App\Http\Controllers\University\ClassSessionController;
use App\Http\Controllers\University\RoomController;
use App\Http\Controllers\University\TeacherController;
use App\Http\Controllers\University\ClassScheduleController;
use App\Http\Controllers\University\FeeConceptController;
use App\Http\Controllers\University\FeeTariffController;
use App\Http\Controllers\University\InvoiceController;
use App\Http\Controllers\University\PaymentController;
use App\Http\Controllers\University\CashRegisterController;
use App\Http\Controllers\University\SupplierController;
use App\Http\Controllers\University\ExpenseCategoryController;
use App\Http\Controllers\University\ExpenseController;
use App\Http\Controllers\University\ExpensePaymentController;
use App\Http\Controllers\University\FinancialReportController;
use App\Http\Controllers\University\ScholarshipTypeController;
use App\Http\Controllers\University\StudentScholarshipController;
use App\Http\Controllers\University\ScholarshipReportController;
use App\Http\Controllers\University\BankAccountController;
use App\Http\Controllers\University\BankMovementController;
use App\Http\Controllers\University\BankReconciliationController;
use App\Http\Controllers\University\BankIntegrationController;
use App\Http\Controllers\University\BankApiTransactionController;
use App\Http\Controllers\University\BankReportController;
use App\Http\Controllers\University\TreasuryMovementController;
use App\Http\Controllers\University\FinancialAuditController;
use App\Http\Controllers\University\TreasuryController;
use App\Http\Controllers\University\TreasuryOperationController;
use App\Http\Controllers\University\TreasuryDailyClosureController;
use App\Http\Controllers\University\TreasuryDailyReportController;
use App\Http\Controllers\University\TreasuryPositionController;
use App\Http\Controllers\University\TreasuryCashFlowController;
use App\Http\Controllers\University\TreasuryOperatorController;
use App\Http\Controllers\University\CashTransferController;
use App\Http\Controllers\University\CashCountController;
use App\Http\Controllers\University\PettyCashFundController;
use App\Http\Controllers\University\PettyCashMovementController;
use App\Http\Controllers\University\PettyCashReplenishmentController;
use App\Http\Controllers\University\PettyCashReportController;


use App\Http\Controllers\Webhooks\BankWebhookController;

/*
|--------------------------------------------------------------------------
| Página inicial
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| ÁREA DA UNIVERSIDADE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', EnsureTenant::class])
    ->prefix('universidade')
    ->name('university.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | UTILIZADORES
        |--------------------------------------------------------------------------
        */

        Route::middleware('can:users.view')->prefix('utilizadores')->name('users.')->controller(UserController::class)->group(function () {

            Route::get('/', 'index')->name('index');

            Route::get('/criar', 'create')
                ->middleware('can:users.create')
                ->name('create');

            Route::post('/', 'store')
                ->middleware('can:users.create')
                ->name('store');

            Route::get('/{userId}/editar', 'edit')
                ->middleware('can:users.update')
                ->name('edit');

            Route::put('/{userId}', 'update')
                ->middleware('can:users.update')
                ->name('update');

            Route::patch('/{userId}/estado', 'updateStatus')
                ->middleware('can:users.update')
                ->name('status');

            Route::delete('/{userId}', 'destroy')
                ->middleware('can:users.delete')
                ->name('destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | FACULDADES
        |--------------------------------------------------------------------------
        */

        Route::middleware('can:faculties.view')->prefix('faculdades')->name('faculties.')->controller(FacultyController::class)->group(function () {

            Route::get('/', 'index')->name('index');

            Route::get('/criar', 'create')
                ->middleware('can:faculties.create')
                ->name('create');

            Route::post('/', 'store')
                ->middleware('can:faculties.create')
                ->name('store');

            Route::get('/{id}/editar', 'edit')
                ->middleware('can:faculties.update')
                ->name('edit');

            Route::put('/{id}', 'update')
                ->middleware('can:faculties.update')
                ->name('update');

            Route::patch('/{id}/estado', 'updateStatus')
                ->middleware('can:faculties.update')
                ->name('status');

            Route::delete('/{id}', 'destroy')
                ->middleware('can:faculties.delete')
                ->name('destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | CURSOS
        |--------------------------------------------------------------------------
        */

        Route::middleware('can:courses.view')->prefix('cursos')->name('courses.')->controller(CourseController::class)->group(function () {

            Route::get('/', 'index')->name('index');

            Route::get('/criar', 'create')
                ->middleware('can:courses.create')
                ->name('create');

            Route::post('/', 'store')
                ->middleware('can:courses.create')
                ->name('store');

            Route::get('/{id}/editar', 'edit')
                ->middleware('can:courses.update')
                ->name('edit');

            Route::put('/{id}', 'update')
                ->middleware('can:courses.update')
                ->name('update');

            Route::patch('/{id}/estado', 'updateStatus')
                ->middleware('can:courses.update')
                ->name('status');

            Route::delete('/{id}', 'destroy')
                ->middleware('can:courses.delete')
                ->name('destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | DEPARTAMENTOS
        |--------------------------------------------------------------------------
        */

        Route::middleware('can:departments.view')->prefix('departamentos')->name('departments.')->controller(DepartmentController::class)->group(function () {

            Route::get('/', 'index')->name('index');

            Route::get('/criar', 'create')
                ->middleware('can:departments.create')
                ->name('create');

            Route::post('/', 'store')
                ->middleware('can:departments.create')
                ->name('store');

            Route::get('/{id}/editar', 'edit')
                ->middleware('can:departments.update')
                ->name('edit');

            Route::put('/{id}', 'update')
                ->middleware('can:departments.update')
                ->name('update');

            Route::patch('/{id}/estado', 'updateStatus')
                ->middleware('can:departments.update')
                ->name('status');

            Route::delete('/{id}', 'destroy')
                ->middleware('can:departments.delete')
                ->name('destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | DISCIPLINAS
        |--------------------------------------------------------------------------
        */

        Route::middleware('can:subjects.view')->prefix('disciplinas')->name('subjects.')->controller(SubjectController::class)->group(function () {

            Route::get('/', 'index')->name('index');

            Route::get('/criar', 'create')
                ->middleware('can:subjects.create')
                ->name('create');

            Route::post('/', 'store')
                ->middleware('can:subjects.create')
                ->name('store');

            Route::get('/{id}/editar', 'edit')
                ->middleware('can:subjects.update')
                ->name('edit');

            Route::put('/{id}', 'update')
                ->middleware('can:subjects.update')
                ->name('update');

            Route::patch('/{id}/estado', 'updateStatus')
                ->middleware('can:subjects.update')
                ->name('status');

            Route::delete('/{id}', 'destroy')
                ->middleware('can:subjects.delete')
                ->name('destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | CURRÍCULOS
        |--------------------------------------------------------------------------
        */

        Route::prefix('curriculos')
            ->name('curricula.')
            ->controller(CurriculumController::class)
            ->group(function () {

                Route::get('/', 'index')
                    ->middleware('can:curricula.view')
                    ->name('index');

                Route::get('/criar', 'create')
                    ->middleware('can:curricula.create')
                    ->name('create');

                Route::post('/', 'store')
                    ->middleware('can:curricula.create')
                    ->name('store');

                Route::get('/{curriculum}/editar', 'edit')
                    ->middleware('can:curricula.view')
                    ->name('edit');

                Route::put('/{curriculum}', 'update')
                    ->middleware('can:curricula.update')
                    ->name('update');

                Route::patch('/{curriculum}/estado', 'updateStatus')
                    ->middleware('can:curricula.update')
                    ->name('status');

                Route::delete('/{curriculum}', 'destroy')
                    ->middleware('can:curricula.delete')
                    ->name('destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | DISCIPLINAS DOS CURRÍCULOS
        |--------------------------------------------------------------------------
        */

        Route::prefix('curriculos/{curriculum}/disciplinas')
            ->name('curricula.subjects.')
            ->controller(CurriculumSubjectController::class)
            ->middleware('can:curricula.update')
            ->group(function () {

                Route::post('/', 'store')->name('store');

                Route::put('/{curriculumSubject}', 'update')
                    ->name('update');

                Route::delete('/{curriculumSubject}', 'destroy')
                    ->name('destroy');

                Route::post('/{curriculumSubject}/pre-requisitos', 'storePrerequisite')
                    ->name('prerequisites.store');

                Route::delete('/{curriculumSubject}/pre-requisitos/{prerequisite}', 'destroyPrerequisite')
                    ->name('prerequisites.destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | ANOS LECTIVOS
        |--------------------------------------------------------------------------
        */

        Route::prefix('anos-lectivos')
            ->name('academic-years.')
            ->controller(AcademicYearController::class)
            ->group(function () {

                Route::get('/', 'index')
                    ->middleware('can:academic_years.view')
                    ->name('index');

                Route::get('/criar', 'create')
                    ->middleware('can:academic_years.create')
                    ->name('create');

                Route::post('/', 'store')
                    ->middleware('can:academic_years.create')
                    ->name('store');

                Route::get('/{academicYear}/editar', 'edit')
                    ->middleware('can:academic_years.update')
                    ->name('edit');

                Route::put('/{academicYear}', 'update')
                    ->middleware('can:academic_years.update')
                    ->name('update');

                Route::patch('/{academicYear}/estado', 'updateStatus')
                    ->middleware('can:academic_years.update')
                    ->name('status');

                Route::patch('/{academicYear}/actual', 'setCurrent')
                    ->middleware('can:academic_years.update')
                    ->name('current');

                Route::delete('/{academicYear}', 'destroy')
                    ->middleware('can:academic_years.delete')
                    ->name('destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | TURMAS E CLASSES
        |--------------------------------------------------------------------------
        */

        Route::prefix('turmas')
            ->name('classes.')
            ->controller(AcademicClassController::class)
            ->group(function () {

                Route::get('/', 'index')
                    ->middleware('can:classes.view')
                    ->name('index');

                Route::get('/criar', 'create')
                    ->middleware('can:classes.create')
                    ->name('create');

                Route::post('/', 'store')
                    ->middleware('can:classes.create')
                    ->name('store');

                Route::get('/{academicClass}/editar', 'edit')
                    ->middleware('can:classes.update')
                    ->name('edit');

                Route::put('/{academicClass}', 'update')
                    ->middleware('can:classes.update')
                    ->name('update');

                Route::patch('/{academicClass}/estado', 'updateStatus')
                    ->middleware('can:classes.update')
                    ->name('status');

                Route::delete('/{academicClass}', 'destroy')
                    ->middleware('can:classes.delete')
                    ->name('destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | PERFIS E PERMISSÕES
        |--------------------------------------------------------------------------
        */

        Route::prefix('perfis')
            ->name('roles.')
            ->controller(RoleController::class)
            ->group(function () {

                // Atenção: aplicar aqui as permissões adequadas
                Route::get('/', 'index')->name('index');

                Route::get('/criar', 'create')->name('create');

                Route::post('/', 'store')->name('store');

                Route::get('/{id}/editar', 'edit')->name('edit');

                Route::put('/{id}', 'update')->name('update');

                Route::delete('/{id}', 'destroy')->name('destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | ESTUDANTES
        |--------------------------------------------------------------------------
        */

        Route::prefix('estudantes')
            ->name('students.')
            ->controller(StudentController::class)
            ->group(function () {

                Route::get('/', 'index')
                    ->middleware('can:students.view')
                    ->name('index');

                Route::get('/criar', 'create')
                    ->middleware('can:students.create')
                    ->name('create');

                Route::post('/', 'store')
                    ->middleware('can:students.create')
                    ->name('store');
                    
                Route::get('/{student}', 'show')
                    ->middleware('can:students.view')
                    ->name('show');

                Route::get('/{student}/editar', 'edit')
                    ->middleware('can:students.update')
                    ->name('edit');

                Route::put('/{student}', 'update')
                    ->middleware('can:students.update')
                    ->name('update');


                Route::patch('/{student}/estado', 'updateStatus')
                    ->middleware('can:students.update')
                    ->name('status');
            });


        /*
        |--------------------------------------------------------------------------
        | MATRÍCULAS
        |--------------------------------------------------------------------------
        */

        Route::prefix('matriculas')->name('enrollments.')->controller(EnrollmentController::class)->group(function () {
            Route::get('/', 'index')->middleware('can:enrollments.view')->name('index');
            Route::get('/criar', 'create')->middleware('can:enrollments.create')->name('create');
            Route::post('/', 'store')->middleware('can:enrollments.create')->name('store');
            Route::get('/{enrollment}/imprimir', 'print')->middleware('can:enrollments.view')->name('print');
            Route::get('/{enrollment}', 'show')->middleware('can:enrollments.view')->name('show');
            Route::patch('/{enrollment}/estado', 'updateStatus')->middleware('can:enrollments.update')->name('update-status');
        });

        // Avaliações e notas
        Route::middleware('can:assessments.view')->group(function () {
            Route::get('/turmas/{academicClass}/avaliacoes',[SubjectAssessmentController::class, 'index'])->name('assessments.index');
            Route::get('/avaliacoes/{assessment}',[SubjectAssessmentController::class, 'show'])->name('assessments.show');
        });
        Route::middleware('can:assessments.create')->group(function () {
            Route::get('/turmas/{academicClass}/avaliacoes/criar',[SubjectAssessmentController::class, 'create'])->name('assessments.create');
            Route::post('/turmas/{academicClass}/avaliacoes',[SubjectAssessmentController::class, 'store'])->name('assessments.store');
        });
        Route::middleware('can:assessments.update')->group(function () {
            Route::put('/avaliacoes/{assessment}/notas',[SubjectAssessmentController::class, 'saveGrades'])->name('assessments.save-grades');
        });
        Route::middleware('can:assessments.publish')->group(function () {
            Route::patch('/avaliacoes/{assessment}/publicar',[SubjectAssessmentController::class, 'publish'])->name('assessments.publish');
        });
        Route::get('/turmas/{academicClass}/disciplinas/{curriculumSubject}/pauta',[SubjectAssessmentController::class, 'gradeSheet'])->middleware('can:assessments.view')->name('assessments.grade-sheet');

        /*
        |--------------------------------------------------------------------------
        | INSCRIÇÕES EM DISCIPLINAS
        |--------------------------------------------------------------------------
        */

        Route::prefix('matriculas/{enrollment}/disciplinas')->name('subject-enrollments.')->controller(SubjectEnrollmentController::class)->group(function () {
            Route::get('/', 'index')->middleware('can:subject_enrollments.view')->name('index');
            Route::get('/criar', 'create')->middleware('can:subject_enrollments.create')->name('create');
            Route::post('/', 'store')->middleware('can:subject_enrollments.create')->name('store');
            Route::patch('/{subjectEnrollment}/estado', 'updateStatus')->middleware('can:subject_enrollments.update')->name('update-status');
            Route::delete('/{subjectEnrollment}', 'destroy')->middleware('can:subject_enrollments.update')->name('destroy');
        });

        Route::prefix('turmas/{academicClass}/aulas')->name('attendance.sessions.')->group(function () {
            Route::get('/',[ClassSessionController::class, 'index'])->middleware('can:attendance.view')->name('index');
            Route::get('/criar',[ClassSessionController::class, 'create'])->middleware('can:attendance.create')->name('create');
            Route::post('/',[ClassSessionController::class, 'store'])->middleware('can:attendance.create')->name('store');
            Route::get('/{session}/editar',[ClassSessionController::class, 'edit'])->middleware('can:attendance.update')->name('edit');
            Route::put('/{session}',[ClassSessionController::class, 'update'])->middleware('can:attendance.update')->name('update');
            Route::delete('/{session}',[ClassSessionController::class, 'destroy'])->middleware('can:attendance.delete')->name('destroy');
        });

    Route::get('/aulas/{session}/frequencia',[AttendanceController::class, 'show'])->middleware('can:attendance.view')->name('attendance.show');
    Route::post('/aulas/{session}/frequencia',[AttendanceController::class, 'save'])->middleware('can:attendance.register')->name('attendance.save');
    Route::get('/inscricoes-disciplinas/{subjectEnrollment}/frequencia',[AttendanceController::class, 'report'])->middleware('can:attendance.reports')->name('attendance.report');

    Route::get('/salas',[RoomController::class, 'index'])->middleware('can:rooms.view')->name('rooms.index');
    Route::get('/salas/criar',[RoomController::class, 'create'])->middleware('can:rooms.create')->name('rooms.create');
    Route::post('/salas',[RoomController::class, 'store'])->middleware('can:rooms.create')->name('rooms.store');
    Route::get('/salas/{room}/editar',[RoomController::class, 'edit'])->middleware('can:rooms.update')->name('rooms.edit');
    Route::put('/salas/{room}',[RoomController::class, 'update'])->middleware('can:rooms.update')->name('rooms.update');
    Route::delete('/salas/{room}',[RoomController::class, 'destroy'])->middleware('can:rooms.delete')->name('rooms.destroy');

    Route::get('/professores',[TeacherController::class, 'index'])->middleware('can:teachers.view')->name('teachers.index');
    Route::get('/professores/criar',[TeacherController::class, 'create'])->middleware('can:teachers.create')->name('teachers.create');
    Route::post('/professores',[TeacherController::class, 'store'])->middleware('can:teachers.create')->name('teachers.store');
    Route::get('/professores/{teacher}/editar',[TeacherController::class, 'edit'])->middleware('can:teachers.update')->name('teachers.edit');
    Route::put('/professores/{teacher}',[TeacherController::class, 'update'])->middleware('can:teachers.update')->name('teachers.update');
    Route::delete('/professores/{teacher}',[TeacherController::class, 'destroy'])->middleware('can:teachers.delete')->name('teachers.destroy');


    Route::prefix('turmas/{academicClass}/horarios')->name('schedules.')->group(function () {
        Route::get('/',[ClassScheduleController::class, 'index'])->middleware('can:schedules.view')->name('index');
        Route::get('/criar',[ClassScheduleController::class, 'create'])->middleware('can:schedules.create')->name('create');
        Route::post('/',[ClassScheduleController::class, 'store'])->middleware('can:schedules.create')->name('store');
        Route::get('/{schedule}/editar',[ClassScheduleController::class, 'edit'])->middleware('can:schedules.update')->name('edit');
        Route::put('/{schedule}',[ClassScheduleController::class, 'update'])->middleware('can:schedules.update')->name('update');
        Route::delete('/{schedule}',[ClassScheduleController::class, 'destroy'])->middleware('can:schedules.delete')->name('destroy');
    });


    // Gestão Financeira - Conceitos de Cobrança
    Route::prefix('financeiro/conceitos')->name('fee-concepts.')->group(function () {
        Route::get('/', [FeeConceptController::class, 'index'])->middleware('can:finance.fee-concepts.view')->name('index');
        Route::get('/criar', [FeeConceptController::class, 'create'])->middleware('can:finance.fee-concepts.create')->name('create');
        Route::post('/', [FeeConceptController::class, 'store'])->middleware('can:finance.fee-concepts.create')->name('store');
        Route::get('/{feeConcept}/editar', [FeeConceptController::class, 'edit'])->middleware('can:finance.fee-concepts.update')->name('edit');
        Route::put('/{feeConcept}', [FeeConceptController::class, 'update'])->middleware('can:finance.fee-concepts.update')->name('update');
        Route::patch('/{feeConcept}/estado', [FeeConceptController::class, 'toggleStatus'])->middleware('can:finance.fee-concepts.update')->name('toggle-status');
        Route::delete('/{feeConcept}', [FeeConceptController::class, 'destroy'])->middleware('can:finance.fee-concepts.delete')->name('destroy');
    });

    Route::prefix('financeiro/tarifas')->name('fee-tariffs.')->group(function () {
        Route::get('/', [FeeTariffController::class, 'index'])->middleware('can:finance.fee-tariffs.view')->name('index');
        Route::get('/criar', [FeeTariffController::class, 'create'])->middleware('can:finance.fee-tariffs.create')->name('create');
        Route::post('/', [FeeTariffController::class, 'store'])->middleware('can:finance.fee-tariffs.create')->name('store');
        Route::get('/{feeTariff}/editar', [FeeTariffController::class, 'edit'])->middleware('can:finance.fee-tariffs.update')->name('edit');
        Route::put('/{feeTariff}', [FeeTariffController::class, 'update'])->middleware('can:finance.fee-tariffs.update')->name('update');
        Route::patch('/{feeTariff}/estado', [FeeTariffController::class, 'toggleStatus'])->middleware('can:finance.fee-tariffs.update')->name('toggle-status');
        Route::delete('/{feeTariff}', [FeeTariffController::class, 'destroy'])->middleware('can:finance.fee-tariffs.delete')->name('destroy');
    });


    Route::prefix('financeiro/facturas')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->middleware('can:finance.invoices.view')->name('index');
        Route::get('/criar', [InvoiceController::class, 'create'])->middleware('can:finance.invoices.create')->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->middleware('can:finance.invoices.create')->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->middleware('can:finance.invoices.view')->name('show');
        Route::patch('/{invoice}/emitir', [InvoiceController::class, 'issue'])->middleware('can:finance.invoices.issue')->name('issue');
        Route::patch('/{invoice}/cancelar', [InvoiceController::class, 'cancel'])->middleware('can:finance.invoices.cancel')->name('cancel');
    });


    Route::prefix('financeiro/pagamentos')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->middleware('can:finance.payments.view')->name('index');
        Route::get('/factura/{invoice}/criar', [PaymentController::class, 'create'])->middleware('can:finance.payments.create')->name('create');
        Route::post('/factura/{invoice}', [PaymentController::class, 'store'])->middleware('can:finance.payments.create')->name('store');
        Route::patch('/{payment}/confirmar', [PaymentController::class, 'confirm'])->middleware('can:finance.payments.confirm')->name('confirm');
        Route::patch('/{payment}/anular', [PaymentController::class, 'cancel'])->middleware('can:finance.payments.cancel')->name('cancel');
        Route::patch('/{payment}/reverter', [PaymentController::class, 'reverse'])->middleware('can:finance.payments.reverse')->name('reverse');
        Route::get('/{payment}/recibo', [PaymentController::class, 'receipt'])->middleware('can:finance.payments.receipt')->name('receipt');
    });



    Route::prefix('financeiro/fornecedores')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->middleware('can:finance.suppliers.view')->name('index');
        Route::get('/criar', [SupplierController::class, 'create'])->middleware('can:finance.suppliers.create')->name('create');
        Route::post('/', [SupplierController::class, 'store'])->middleware('can:finance.suppliers.create')->name('store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->middleware('can:finance.suppliers.view')->name('show');
        Route::get('/{supplier}/editar', [SupplierController::class, 'edit'])->middleware('can:finance.suppliers.update')->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->middleware('can:finance.suppliers.update')->name('update');
        Route::patch('/{supplier}/estado', [SupplierController::class, 'toggleStatus'])->middleware('can:finance.suppliers.delete')->name('toggle-status');
    });

    Route::prefix('financeiro/categorias-despesas')->name('expense-categories.')->group(function () {
        Route::get('/', [ExpenseCategoryController::class, 'index'])->middleware('can:finance.expense-categories.view')->name('index');
        Route::get('/criar', [ExpenseCategoryController::class, 'create'])->middleware('can:finance.expense-categories.create')->name('create');
        Route::post('/', [ExpenseCategoryController::class, 'store'])->middleware('can:finance.expense-categories.create')->name('store');
        Route::get('/{expenseCategory}', [ExpenseCategoryController::class, 'show'])->middleware('can:finance.expense-categories.view')->name('show');
        Route::get('/{expenseCategory}/editar', [ExpenseCategoryController::class, 'edit'])->middleware('can:finance.expense-categories.update')->name('edit');
        Route::put('/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->middleware('can:finance.expense-categories.update')->name('update');
        Route::patch('/{expenseCategory}/estado', [ExpenseCategoryController::class, 'toggleStatus'])->middleware('can:finance.expense-categories.delete')->name('toggle-status');
    });


    Route::prefix('financeiro/despesas')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->middleware('can:finance.expenses.view')->name('index');
        Route::get('/criar', [ExpenseController::class, 'create'])->middleware('can:finance.expenses.create')->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->middleware('can:finance.expenses.create')->name('store');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->middleware('can:finance.expenses.view')->name('show');
        Route::get('/{expense}/editar', [ExpenseController::class, 'edit'])->middleware('can:finance.expenses.update')->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->middleware('can:finance.expenses.update')->name('update');
        Route::patch('/{expense}/aprovar', [ExpenseController::class, 'approve'])->middleware('can:finance.expenses.approve')->name('approve');
        Route::patch('/{expense}/cancelar', [ExpenseController::class, 'cancel'])->middleware('can:finance.expenses.cancel')->name('cancel');
    });

    Route::prefix('financeiro/despesas')->name('expenses.')->group(function () {
        Route::get('/{expense}/pagamentos/criar', [ExpensePaymentController::class,'create'])->middleware('can:finance.expenses.payments.create')->name('payments.create');
        Route::post('/{expense}/pagamentos', [ExpensePaymentController::class,'store'])->middleware('can:finance.expenses.payments.create')->name('payments.store');
    });

    Route::get('/financeiro/relatorios', [FinancialReportController::class,'index'])->middleware('can:finance.reports.view')->name('financial-reports.index');
    Route::prefix('financeiro/relatorios')->name('financial-reports.')->middleware('can:finance.reports.view')->group(function () {
        Route::get('/exportar/excel', [FinancialReportController::class,'exportExcel'])->name('excel');
        Route::get('/exportar/pdf', [FinancialReportController::class,'exportPdf'])->name('pdf');
    });

    // Caixa e Tesouraria
    // Gestão de Caixa
    Route::prefix('financeiro/caixa')->name('cash.')->controller(CashRegisterController::class)->group(function () {
        Route::get('/', 'index')->middleware('can:finance.cash.view')->name('index');
        Route::get('/abrir', 'create')->middleware('can:finance.cash.open')->name('create');
        Route::post('/abrir', 'store')->middleware('can:finance.cash.open')->name('store');
        Route::get('/{cashRegister}', 'show')->middleware('can:finance.cash.view')->name('show');
        Route::get('/{cashRegister}/movimento/criar', 'movementForm')->middleware('can:finance.cash.movements.create')->name('movement.create');
        Route::post('/{cashRegister}/movimento', 'storeMovement')->middleware('can:finance.cash.movements.create')->name('movement.store');
        Route::get('/{cashRegister}/fechar', 'closeForm')->middleware('can:finance.cash.close')->name('close.create');
        Route::post('/{cashRegister}/fechar', 'close')->middleware('can:finance.cash.close')->name('close.store');
    });

    Route::prefix('financeiro/bolsas/tipos')->name('scholarship-types.')->group(function () {
        Route::get('/', [ScholarshipTypeController::class, 'index'])->middleware('can:scholarships.types.view')->name('index');
        Route::get('/criar', [ScholarshipTypeController::class, 'create'])->middleware('can:scholarships.types.create')->name('create');
        Route::post('/', [ScholarshipTypeController::class, 'store'])->middleware('can:scholarships.types.create')->name('store');
        Route::get('/{scholarshipType}', [ScholarshipTypeController::class, 'show'])->middleware('can:scholarships.types.view')->name('show');
        Route::get('/{scholarshipType}/editar', [ScholarshipTypeController::class, 'edit'])->middleware('can:scholarships.types.update')->name('edit');
        Route::put('/{scholarshipType}', [ScholarshipTypeController::class, 'update'])->middleware('can:scholarships.types.update')->name('update');
        Route::delete('/{scholarshipType}', [ScholarshipTypeController::class, 'destroy'])->middleware('can:scholarships.types.delete')->name('destroy');
    });


    Route::prefix('financeiro/bolsas/estudantes')->name('student-scholarships.')->group(function () {
        Route::get('/', [StudentScholarshipController::class, 'index'])->middleware('can:scholarships.students.view')->name('index');
        Route::get('/criar', [StudentScholarshipController::class, 'create'])->middleware('can:scholarships.students.create')->name('create');
        Route::post('/', [StudentScholarshipController::class, 'store'])->middleware('can:scholarships.students.create')->name('store');
        Route::get('/{studentScholarship}', [StudentScholarshipController::class, 'show'])->middleware('can:scholarships.students.view')->name('show');
        Route::post('/{studentScholarship}/aprovar', [StudentScholarshipController::class, 'approve'])->middleware('can:scholarships.students.approve')->name('approve');
        Route::post('/{studentScholarship}/rejeitar', [StudentScholarshipController::class, 'reject'])->middleware('can:scholarships.students.reject')->name('reject');
        Route::post('/{studentScholarship}/suspender', [StudentScholarshipController::class, 'suspend'])->middleware('can:scholarships.students.suspend')->name('suspend');
    });


    Route::post('/facturas/{invoice}/aplicar-bolsa',[InvoiceController::class, 'applyScholarship'])->middleware('can:finance.invoices.update')->name('invoices.apply-scholarship');
    Route::delete('/facturas/{invoice}/remover-bolsa',[InvoiceController::class, 'removeScholarship'])->middleware('can:finance.invoices.update')->name('invoices.remove-scholarship');

    Route::get('/financeiro/bolsas/relatorios',[ScholarshipReportController::class, 'index'])->middleware('can:scholarships.reports.view')->name('scholarships.reports');

    Route::prefix('financeiro/contas-bancarias')->name('bank-accounts.')->group(function () {
        Route::get('/', [BankAccountController::class, 'index'])->middleware('can:finance.bank-accounts.view')->name('index');
        Route::get('/criar', [BankAccountController::class, 'create'])->middleware('can:finance.bank-accounts.create')->name('create');
        Route::post('/', [BankAccountController::class, 'store'])->middleware('can:finance.bank-accounts.create')->name('store');
        Route::get('/{bankAccount}', [BankAccountController::class, 'show'])->middleware('can:finance.bank-accounts.view')->name('show');
        Route::get('/{bankAccount}/editar', [BankAccountController::class, 'edit'])->middleware('can:finance.bank-accounts.update')->name('edit');
        Route::put('/{bankAccount}', [BankAccountController::class, 'update'])->middleware('can:finance.bank-accounts.update')->name('update');
        Route::delete('/{bankAccount}', [BankAccountController::class, 'destroy'])->middleware('can:finance.bank-accounts.delete')->name('destroy');
    });

    Route::prefix('financeiro/movimentos-bancarios')->name('bank-movements.')->group(function () {
        Route::get('/', [BankMovementController::class, 'index'])->middleware('can:finance.bank-movements.view')->name('index');
        Route::get('/criar', [BankMovementController::class, 'create'])->middleware('can:finance.bank-movements.create')->name('create');
        Route::post('/', [BankMovementController::class, 'store'])->middleware('can:finance.bank-movements.create')->name('store');
        Route::get('/{bankMovement}', [BankMovementController::class, 'show'])->middleware('can:finance.bank-movements.view')->name('show');
        Route::get('/transferencia/criar', [BankMovementController::class, 'transferCreate'])->middleware('can:finance.bank-movements.create')->name('transfer.create');
        Route::post('/transferencia', [BankMovementController::class, 'transferStore'])->middleware('can:finance.bank-movements.create')->name('transfer.store');
    });

    Route::prefix('financeiro/reconciliacoes')->name('bank-reconciliations.')->group(function () {
        Route::get('/', [BankReconciliationController::class,'index'])->middleware('can:finance.bank-reconciliations.view')->name('index');
        Route::get('/criar', [BankReconciliationController::class,'create'])->middleware('can:finance.bank-reconciliations.create')->name('create');
        Route::post('/', [BankReconciliationController::class,'store'])->middleware('can:finance.bank-reconciliations.create')->name('store');
        Route::get('/{bankReconciliation}', [BankReconciliationController::class,'show'])->middleware('can:finance.bank-reconciliations.view')->name('show');
        Route::get('/{bankReconciliation}/importar', [BankReconciliationController::class,'importForm'])->middleware('can:finance.bank-reconciliations.create')->name('import');
        Route::post('/{bankReconciliation}/importar', [BankReconciliationController::class,'import'])->middleware('can:finance.bank-reconciliations.create')->name('import.store');
        Route::post('/{bankReconciliation}/linhas/{line}/conciliar', [BankReconciliationController::class,'match'])->middleware('can:finance.bank-reconciliations.match')->name('match');
        Route::delete('/{bankReconciliation}/linhas/{line}/conciliar', [BankReconciliationController::class,'unmatch'])->middleware('can:finance.bank-reconciliations.match')->name('unmatch');
        Route::post('/{bankReconciliation}/linhas/{line}/ignorar', [BankReconciliationController::class,'ignore'])->middleware('can:finance.bank-reconciliations.match')->name('ignore');
        Route::post('/{bankReconciliation}/concluir', [BankReconciliationController::class,'reconcile'])->middleware('can:finance.bank-reconciliations.reconcile')->name('reconcile');
    });


    Route::prefix('financeiro/integracoes-bancarias')->name('bank-integrations.')->group(function () {
        Route::get('/', [BankIntegrationController::class, 'index'])->middleware('can:finance.bank-integrations.view')->name('index');
        Route::get('/criar', [BankIntegrationController::class, 'create'])->middleware('can:finance.bank-integrations.create')->name('create');
        Route::post('/', [BankIntegrationController::class, 'store'])->middleware('can:finance.bank-integrations.create')->name('store');
        Route::get('/{bankIntegration}', [BankIntegrationController::class, 'show'])->middleware('can:finance.bank-integrations.view')->name('show');
        Route::get('/{bankIntegration}/editar', [BankIntegrationController::class, 'edit'])->middleware('can:finance.bank-integrations.update')->name('edit');
        Route::put('/{bankIntegration}', [BankIntegrationController::class, 'update'])->middleware('can:finance.bank-integrations.update')->name('update');
        Route::patch('/{bankIntegration}/desactivar', [BankIntegrationController::class, 'disable'])->middleware('can:finance.bank-integrations.update')->name('disable');
    });

    Route::post('/webhooks/bancos/{publicId}',BankWebhookController::class)->name('webhooks.banks.receive');

    Route::prefix('financeiro/transacoes-bancarias')->name('bank-api-transactions.')->group(function () {
        Route::get('/', [BankApiTransactionController::class, 'index'])->middleware('can:finance.bank-transactions.view')->name('index');
        Route::get('/{transaction}', [BankApiTransactionController::class, 'show'])->middleware('can:finance.bank-transactions.view')->name('show');
        Route::post('/{transaction}/confirmar', [BankApiTransactionController::class, 'confirm'])->middleware('can:finance.bank-transactions.confirm')->name('confirm');
        Route::get('/{transaction}/associar',[BankApiTransactionController::class, 'associateForm'])->middleware('can:finance.bank-transactions.view')->name('associate.form');
        Route::post('/{transaction}/associar',[BankApiTransactionController::class, 'associate'])->middleware('can:finance.bank-transactions.confirm')->name('associate');
    });

    Route::prefix('financeiro/relatorios-bancarios')->name('bank-reports.')->group(function () {
        Route::get('/', [BankReportController::class, 'index'])->middleware('can:finance.bank-reports.view')->name('index');
        Route::get('/exportar/excel', [BankReportController::class, 'exportExcel'])->middleware('can:finance.bank-reports.view')->name('export.excel');
        Route::get('/exportar/pdf', [BankReportController::class, 'exportPdf'])->middleware('can:finance.bank-reports.view')->name('export.pdf');
    });


    Route::prefix('financeiro/auditoria')->name('financial-audit.')->group(function () {
        Route::get('/', [FinancialAuditController::class, 'index'])->middleware('can:finance.audit.view')->name('index');
        Route::get('/{audit}', [FinancialAuditController::class, 'show'])->middleware('can:finance.audit.view')->name('show');
    });

    Route::get('/tesouraria', [TreasuryController::class, 'index'])->middleware('can:finance.treasury.view')->name('treasury.dashboard');
    Route::get('/tesouraria/recibos/{payment}', [TreasuryController::class, 'receipt'])->middleware('can:finance.invoices.view')->name('treasury.receipts.show');

    Route::get('/tesouraria/movimentos', [TreasuryMovementController::class, 'index'])->middleware('can:finance.treasury.movements.view')->name('treasury.movements.index');
    Route::get('/tesouraria/operacoes', [TreasuryOperationController::class, 'index'])->middleware('can:finance.treasury.view')->name('treasury.operations.index');

    Route::prefix('tesouraria/fechos-diarios')->name('treasury.daily-closures.')->group(function () {
        Route::get('/', [TreasuryDailyClosureController::class,'index',])->middleware('can:finance.treasury.closures.view')->name('index');
        Route::get('/novo', [TreasuryDailyClosureController::class,'create',])->middleware('can:finance.treasury.closures.create')->name('create');
        Route::post('/', [TreasuryDailyClosureController::class,'store',])->middleware('can:finance.treasury.closures.create')->name('store');
        Route::get('/{closure}', [TreasuryDailyClosureController::class,'show',])->middleware('can:finance.treasury.closures.view')->name('show');
    });

    Route::prefix('tesouraria/relatorios')->name('treasury.reports.')->group(function () {
        Route::get('/diario', [TreasuryDailyReportController::class,'index',])->middleware('can:finance.treasury.reports.view')->name('daily');
        Route::get('/diario/imprimir', [TreasuryDailyReportController::class,'print',])->middleware('can:finance.treasury.reports.view')->name('daily.print');
        Route::get('/movimentos-financeiros', [TreasuryCashFlowController::class,'index',])->middleware('can:finance.treasury.cash-flow.view')->name('cash-flow');
        Route::get('/movimentos-financeiros/imprimir', [TreasuryCashFlowController::class,'print',])->middleware('can:finance.treasury.cash-flow.view')->name('cash-flow.print');
    });

    Route::get('/tesouraria/posicao', [TreasuryPositionController::class,'index',])->middleware('can:finance.treasury.position.view')->name('treasury.position.index');
    
    Route::prefix('tesouraria/operadores')->name('treasury.operators.')->group(function () {
        Route::get('/', [TreasuryOperatorController::class, 'index'])->middleware('can:finance.treasury.operators.view')->name('index');
        Route::get('/caixa/{cashRegister}', [TreasuryOperatorController::class, 'show'])->middleware('can:finance.treasury.operators.view')->name('show');
    });


    Route::prefix('tesouraria/transferencias')->name('treasury.transfers.')->group(function () {
        Route::get('/', [CashTransferController::class, 'index'])->middleware('can:finance.treasury.transfers.view')->name('index');
        Route::get('/nova', [CashTransferController::class, 'create'])->middleware('can:finance.treasury.transfers.create')->name('create');
        Route::post('/', [CashTransferController::class, 'store'])->middleware('can:finance.treasury.transfers.create')->name('store');
        Route::get('/{cashTransfer}', [CashTransferController::class, 'show'])->middleware('can:finance.treasury.transfers.view')->name('show');
        Route::post('/{cashTransfer}/receber', [CashTransferController::class, 'receive'])->middleware('can:finance.treasury.transfers.receive')->name('receive');
        Route::post('/{cashTransfer}/rejeitar', [CashTransferController::class, 'reject'])->middleware('can:finance.treasury.transfers.receive')->name('reject');
        Route::post('/{cashTransfer}/cancelar', [CashTransferController::class, 'cancel'])->middleware('can:finance.treasury.transfers.create')->name('cancel');
    });


    Route::prefix('tesouraria/contagens')
    ->name('treasury.cash-counts.')
    ->group(function () {
        Route::get('/', [CashCountController::class, 'index'])->middleware('can:finance.treasury.cash-counts.view')->name('index');
        Route::get('/nova', [CashCountController::class, 'create'])->middleware('can:finance.treasury.cash-counts.create')->name('create');
        Route::post('/', [CashCountController::class, 'store'])->middleware('can:finance.treasury.cash-counts.create')->name('store');
        Route::get('/{cashCount}', [CashCountController::class, 'show'])->middleware('can:finance.treasury.cash-counts.view')->name('show');
        Route::post('/{cashCount}/aprovar', [CashCountController::class, 'approve'])->middleware('can:finance.treasury.cash-counts.approve')->name('approve');
        Route::post('/{cashCount}/rejeitar', [CashCountController::class, 'reject'])->middleware('can:finance.treasury.cash-counts.approve')->name('reject');
    });


    
    Route::prefix('caixa-pequena')->name('petty-cash.')->group(function () {
        Route::get('/fundos', [PettyCashFundController::class, 'index'])->middleware('can:finance.petty-cash.funds.view')->name('funds.index');
        Route::get('/fundos/novo', [PettyCashFundController::class, 'create'])->middleware('can:finance.petty-cash.funds.create')->name('funds.create');
        Route::post('/fundos', [PettyCashFundController::class, 'store'])->middleware('can:finance.petty-cash.funds.create')->name('funds.store');
        Route::get('/fundos/{pettyCashFund}', [PettyCashFundController::class, 'show'])->middleware('can:finance.petty-cash.funds.view')->name('funds.show');
        Route::get('/fundos/{pettyCashFund}/editar', [PettyCashFundController::class, 'edit'])->middleware('can:finance.petty-cash.funds.update')->name('funds.edit');
        Route::put('/fundos/{pettyCashFund}', [PettyCashFundController::class, 'update'])->middleware('can:finance.petty-cash.funds.update')->name('funds.update');
        Route::patch('/fundos/{pettyCashFund}/estado', [PettyCashFundController::class, 'toggleStatus'])->middleware('can:finance.petty-cash.funds.update')->name('funds.toggle-status');
        Route::get('/fundos/{pettyCashFund}/despesas/nova', [PettyCashMovementController::class, 'create'])->middleware('can:finance.petty-cash.movements.create')->name('movements.create');
        Route::post('/fundos/{pettyCashFund}/despesas', [PettyCashMovementController::class, 'store'])->middleware('can:finance.petty-cash.movements.create')->name('movements.store');
        Route::post('/movimentos/{movement}/aprovar', [PettyCashMovementController::class, 'approve'])->middleware('can:finance.petty-cash.movements.approve')->name('movements.approve');
        Route::post('/movimentos/{movement}/rejeitar', [PettyCashMovementController::class, 'reject'])->middleware('can:finance.petty-cash.movements.approve')->name('movements.reject');
        
        Route::get('/reposicoes', [PettyCashReplenishmentController::class, 'index'])->middleware('can:finance.petty-cash.replenishments.view')->name('replenishments.index');
        Route::get('/fundos/{pettyCashFund}/reposicoes/nova', [PettyCashReplenishmentController::class, 'create'])->middleware('can:finance.petty-cash.replenishments.create')->name('replenishments.create');
        Route::post('/fundos/{pettyCashFund}/reposicoes', [PettyCashReplenishmentController::class, 'store'])->middleware('can:finance.petty-cash.replenishments.create')->name('replenishments.store');
        Route::get('/reposicoes/{replenishment}', [PettyCashReplenishmentController::class, 'show'])->middleware('can:finance.petty-cash.replenishments.view')->name('replenishments.show');
        Route::post('/reposicoes/{replenishment}/aprovar', [PettyCashReplenishmentController::class, 'approve'])->middleware('can:finance.petty-cash.replenishments.approve')->name('replenishments.approve');
        Route::post('/reposicoes/{replenishment}/rejeitar', [PettyCashReplenishmentController::class, 'reject'])->middleware('can:finance.petty-cash.replenishments.approve')->name('replenishments.reject');
        Route::post('/reposicoes/{replenishment}/pagar', [PettyCashReplenishmentController::class, 'pay'])->middleware('can:finance.petty-cash.replenishments.pay')->name('replenishments.pay');
        
        Route::prefix('relatorios')->name('reports.')->middleware('can:finance.petty-cash.reports.view')->group(function () {
            Route::get('/', [PettyCashReportController::class, 'index'])->name('index');
            Route::get('/exportar-csv', [PettyCashReportController::class, 'exportCsv'])->name('csv');
            Route::get('/imprimir', [PettyCashReportController::class, 'print'])->name('print');
        });
    });




});


/*
|--------------------------------------------------------------------------
| ÁREA DA PLATAFORMA
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('plataforma')->name('platform.')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | UNIVERSIDADES
    |--------------------------------------------------------------------------
    */
    Route::prefix('universidades')->name('universities.')->controller(UniversityController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/criar', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{university}/editar', 'edit')->name('edit');
        Route::put('/{university}', 'update')->name('update');
        Route::patch('/{university}/estado', 'updateStatus')->name('status');
    });


    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADORES DAS UNIVERSIDADES
    |--------------------------------------------------------------------------
    */

    Route::prefix('universidades/{university}/administradores')->name('universities.administrators.')->controller(UniversityAdminController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/criar', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::patch('/{user}/estado', 'updateStatus')->name('status');
    });

});


/*
|--------------------------------------------------------------------------
| PERFIL DO UTILIZADOR
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
