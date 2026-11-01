<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureTenant;

use App\Http\Controllers\Platform\UniversityController;
use App\Http\Controllers\Platform\UniversityAdminController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', EnsureTenant::class])->prefix('universidade')->name('university.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    // Gestão de utilizadores
    Route::middleware('can:users.view')->group(function () {
        Route::get('/utilizadores', [UserController::class, 'index'])->name('users.index');
        Route::get('/utilizadores/criar', [UserController::class, 'create'])->middleware('can:users.create')->name('users.create');
        Route::post('/utilizadores', [UserController::class, 'store'])->middleware('can:users.create')->name('users.store');
        Route::get('/utilizadores/{userId}/editar', [UserController::class, 'edit'])->middleware('can:users.update')->name('users.edit');
        Route::put('/utilizadores/{userId}', [UserController::class, 'update'])->middleware('can:users.update')->name('users.update');
        Route::patch('/utilizadores/{userId}/estado', [UserController::class, 'updateStatus'])->middleware('can:users.update')->name('users.status');
        Route::delete('/utilizadores/{userId}', [UserController::class, 'destroy'])->middleware('can:users.delete')->name('users.destroy');
    });

    // Gestão de faculdades
    Route::middleware('can:faculties.view')->group(function () {
        Route::get('/faculdades', [FacultyController::class, 'index'])->name('faculties.index');
        Route::get('/faculdades/criar', [FacultyController::class, 'create'])->middleware('can:faculties.create')->name('faculties.create');
        Route::post('/faculdades', [FacultyController::class, 'store'])->middleware('can:faculties.create')->name('faculties.store');
        Route::get('/faculdades/{id}/editar', [FacultyController::class, 'edit'])->middleware('can:faculties.update')->name('faculties.edit');
        Route::put('/faculdades/{id}', [FacultyController::class, 'update'])->middleware('can:faculties.update')->name('faculties.update');
        Route::patch('/faculdades/{id}/estado', [FacultyController::class, 'updateStatus'])->middleware('can:faculties.update')->name('faculties.status');
        Route::delete('/faculdades/{id}', [FacultyController::class, 'destroy'])->middleware('can:faculties.delete')->name('faculties.destroy');
    });

    // Gestão de cursos
    Route::middleware('can:courses.view')->group(function () {
        Route::get('/cursos', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/cursos/criar', [CourseController::class, 'create'])->middleware('can:courses.create')->name('courses.create');
        Route::post('/cursos', [CourseController::class, 'store'])->middleware('can:courses.create')->name('courses.store');
        Route::get('/cursos/{id}/editar', [CourseController::class, 'edit'])->middleware('can:courses.update')->name('courses.edit');
        Route::put('/cursos/{id}', [CourseController::class, 'update'])->middleware('can:courses.update')->name('courses.update');
        Route::patch('/cursos/{id}/estado', [CourseController::class, 'updateStatus'])->middleware('can:courses.update')->name('courses.status');
        Route::delete('/cursos/{id}', [CourseController::class, 'destroy'])->middleware('can:courses.delete')->name('courses.destroy');
    });

    // Gestão de departamentos
    Route::middleware('can:departments.view')->group(function () {
        Route::get('/departamentos', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departamentos/criar', [DepartmentController::class, 'create'])->middleware('can:departments.create')->name('departments.create');
        Route::post('/departamentos', [DepartmentController::class, 'store'])->middleware('can:departments.create')->name('departments.store');
        Route::get('/departamentos/{id}/editar', [DepartmentController::class, 'edit'])->middleware('can:departments.update')->name('departments.edit');
        Route::put('/departamentos/{id}', [DepartmentController::class, 'update'])->middleware('can:departments.update')->name('departments.update');
        Route::patch('/departamentos/{id}/estado', [DepartmentController::class, 'updateStatus'])->middleware('can:departments.update')->name('departments.status');
        Route::delete('/departamentos/{id}', [DepartmentController::class, 'destroy'])->middleware('can:departments.delete')->name('departments.destroy');
    });

    // Gestão de disciplinas
    Route::middleware('can:subjects.view')->group(function () {
        Route::get('/disciplinas', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/disciplinas/criar', [SubjectController::class, 'create'])->middleware('can:subjects.create')->name('subjects.create');
        Route::post('/disciplinas', [SubjectController::class, 'store'])->middleware('can:subjects.create')->name('subjects.store');
        Route::get('/disciplinas/{id}/editar', [SubjectController::class, 'edit'])->middleware('can:subjects.update')->name('subjects.edit');
        Route::put('/disciplinas/{id}', [SubjectController::class, 'update'])->middleware('can:subjects.update')->name('subjects.update');
        Route::patch('/disciplinas/{id}/estado', [SubjectController::class, 'updateStatus'])->middleware('can:subjects.update')->name('subjects.status');
        Route::delete('/disciplinas/{id}', [SubjectController::class, 'destroy'])->middleware('can:subjects.delete')->name('subjects.destroy');
    });

    Route::get('/curriculos', [CurriculumController::class, 'index'])->middleware('can:curricula.view')->name('curricula.index');
    Route::get('/curriculos/criar', [CurriculumController::class, 'create'])->middleware('can:curricula.create')->name('curricula.create');
    Route::post('/curriculos', [CurriculumController::class, 'store'])->middleware('can:curricula.create')->name('curricula.store');
    Route::get('/curriculos/{curriculum}/editar', [CurriculumController::class, 'edit'])->middleware('can:curricula.view')->name('curricula.edit');
    Route::put('/curriculos/{curriculum}', [CurriculumController::class, 'update'])->middleware('can:curricula.update')->name('curricula.update');
    Route::patch('/curriculos/{curriculum}/estado', [CurriculumController::class, 'updateStatus'])->middleware('can:curricula.update')->name('curricula.status');
    Route::delete('/curriculos/{curriculum}', [CurriculumController::class, 'destroy'])->middleware('can:curricula.delete')->name('curricula.destroy');

    Route::post('/curriculos/{curriculum}/disciplinas',[CurriculumSubjectController::class, 'store'])->middleware('can:curricula.update')->name('curricula.subjects.store');
    Route::put('/curriculos/{curriculum}/disciplinas/{curriculumSubject}',[CurriculumSubjectController::class, 'update'])->middleware('can:curricula.update')->name('curricula.subjects.update');
    Route::delete('/curriculos/{curriculum}/disciplinas/{curriculumSubject}',[CurriculumSubjectController::class, 'destroy'])->middleware('can:curricula.update')->name('curricula.subjects.destroy');
    Route::post('/curriculos/{curriculum}/disciplinas/{curriculumSubject}/pre-requisitos',[CurriculumSubjectController::class, 'storePrerequisite'])->middleware('can:curricula.update')->name('curricula.subjects.prerequisites.store');
    Route::delete('/curriculos/{curriculum}/disciplinas/{curriculumSubject}/pre-requisitos/{prerequisite}',[CurriculumSubjectController::class, 'destroyPrerequisite'])->middleware('can:curricula.update')->name('curricula.subjects.prerequisites.destroy');

    // ANOS LECTIVOS
    Route::prefix('anos-lectivos')->name('academic-years.')->controller(AcademicYearController::class)->group(function () {
        Route::get('/', 'index')->middleware('can:academic_years.view')->name('index');
        Route::get('/criar', 'create')->middleware('can:academic_years.create')->name('create');
        Route::post('/', 'store')->middleware('can:academic_years.create')->name('store');
        Route::get('/{academicYear}/editar', 'edit')->middleware('can:academic_years.update')->name('edit');
        Route::put('/{academicYear}', 'update')->middleware('can:academic_years.update')->name('update');
        Route::patch('/{academicYear}/estado', 'updateStatus')->middleware('can:academic_years.update')->name('status');
        Route::patch('/{academicYear}/actual', 'setCurrent')->middleware('can:academic_years.update')->name('current');
        Route::delete('/{academicYear}', 'destroy')->middleware('can:academic_years.delete')->name('destroy');
    });


    // TURMAS E CLASSES
    Route::prefix('turmas')->name('classes.')->controller(AcademicClassController::class)->group(function () {
        Route::get('/', 'index')->middleware('can:classes.view')->name('index');
        Route::get('/criar', 'create')->middleware('can:classes.create')->name('create');
        Route::post('/', 'store')->middleware('can:classes.create')->name('store');
        Route::get('/{academicClass}/editar', 'edit')->middleware('can:classes.update')->name('edit');
        Route::put('/{academicClass}', 'update')->middleware('can:classes.update')->name('update');
        Route::patch('/{academicClass}/estado', 'updateStatus')->middleware('can:classes.update')->name('status');
        Route::delete('/{academicClass}', 'destroy')->middleware('can:classes.delete')->name('destroy');
    });


    // Gestão de perfis e permissões
    Route::prefix('perfis')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/criar', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{id}/editar', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{id}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });

    
    // ESTUDANTES
    Route::prefix('estudantes')->name('students.')->controller(StudentController::class)->group(function () {
        Route::get('/', 'index')->middleware('can:students.view')->name('index');
        Route::get('/criar', 'create')->middleware('can:students.create')->name('create');
        Route::post('/', 'store')->middleware('can:students.create')->name('store');
        Route::get('/{student}/editar', 'edit')->middleware('can:students.update')->name('edit');
        Route::put('/{student}', 'update')->middleware('can:students.update')->name('update');
        Route::get('/{student}', 'show')->middleware('can:students.view')->name('show');
        Route::patch('/{student}/estado', 'updateStatus')->middleware('can:students.update')->name('status');
    });


    // Matrículas
    Route::middleware('can:enrollments.view')->group(function () {
        Route::get('/matriculas', [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/matriculas/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
        Route::get('/matriculas/{enrollment}/imprimir', [EnrollmentController::class, 'print'])->name('enrollments.print');
    });

    Route::middleware('can:enrollments.create')->group(function () {
        Route::get('/matriculas/criar', [EnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/matriculas', [EnrollmentController::class, 'store'])->name('enrollments.store');
    });

    Route::middleware('can:enrollments.update')->group(function () {
        Route::patch('/matriculas/{enrollment}/estado', [EnrollmentController::class, 'updateStatus'])->name('enrollments.update-status');
    });

    Route::middleware('can:enrollments.view')->group(function () {
        Route::get('/matriculas', [EnrollmentController::class, 'index'])->name('enrollments.index');
    });

    Route::middleware('can:enrollments.create')->group(function () {
        Route::get('/matriculas/criar', [EnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/matriculas', [EnrollmentController::class, 'store'])->name('enrollments.store');
    });

    Route::middleware('can:enrollments.view')->group(function () {
        Route::get('/matriculas/{enrollment}/imprimir', [EnrollmentController::class, 'print'])->name('enrollments.print');
        Route::get('/matriculas/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    });

    Route::middleware('can:enrollments.update')->group(function () {
        Route::patch('/matriculas/{enrollment}/estado', [EnrollmentController::class, 'updateStatus'])->name('enrollments.update-status');
    });

    // Inscrições em disciplinas
    Route::middleware('can:subject_enrollments.view')->group(function () {
        Route::get('/matriculas/{enrollment}/disciplinas',[SubjectEnrollmentController::class, 'index'])->name('subject-enrollments.index');
    });

    Route::middleware('can:subject_enrollments.create')->group(function () {
        Route::get('/matriculas/{enrollment}/disciplinas/criar',[SubjectEnrollmentController::class, 'create'])->name('subject-enrollments.create');
        Route::post('/matriculas/{enrollment}/disciplinas',[SubjectEnrollmentController::class, 'store'])->name('subject-enrollments.store');
    });

    Route::middleware('can:subject_enrollments.update')->group(function () {
        Route::patch('/matriculas/{enrollment}/disciplinas/{subjectEnrollment}/estado',[SubjectEnrollmentController::class, 'updateStatus'])->name('subject-enrollments.update-status');
        Route::delete('/matriculas/{enrollment}/disciplinas/{subjectEnrollment}',[SubjectEnrollmentController::class, 'destroy'])->name('subject-enrollments.destroy');
    });


Route::middleware(['auth'])->prefix('plataforma')->name('platform.')->group(function () {
    Route::get('/universidades', [UniversityController::class, 'index'])->name('universities.index');
    Route::get('/universidades/criar', [UniversityController::class, 'create'])->name('universities.create');
    Route::post('/universidades', [UniversityController::class, 'store'])->name('universities.store');
    Route::get('/universidades/{university}/editar', [UniversityController::class, 'edit'])->name('universities.edit');
    Route::put('/universidades/{university}', [UniversityController::class, 'update'])->name('universities.update');
    Route::patch('/universidades/{university}/estado', [UniversityController::class,'updateStatus',])->name('universities.status');

    Route::get('/universidades/{university}/administradores',[UniversityAdminController::class, 'index'])->name('universities.administrators.index');
    Route::get('/universidades/{university}/administradores/criar',[UniversityAdminController::class, 'create'])->name('universities.administrators.create');
    Route::post('/universidades/{university}/administradores',[UniversityAdminController::class, 'store'])->name('universities.administrators.store');
    Route::patch('/universidades/{university}/administradores/{user}/estado',[UniversityAdminController::class, 'updateStatus'])->name('universities.administrators.status');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
