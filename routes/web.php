<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentClassesController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\AcademicsController;
use App\Http\Controllers\TeacherMarksController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\AllResultsController;
use App\Http\Controllers\TeacherCourseController;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

/* Grade Entry Routes */
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    // Grade entry routes
    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [TeacherMarksController::class, 'index'])->name('index');
        Route::post('/', [TeacherMarksController::class, 'store'])->name('store');
    });

    // TeacherCourse admin module
    Route::middleware(['auth', 'role:admin'])->group(function() {
        Route::resource('teacher_courses', TeacherCourseController::class)->except(['show']);
    });

    // Student routes
    Route::get('/download-file', [StudentsController::class, 'downloadfile'])->name('downloadfile');
    Route::post('/upload-student-data', [StudentsController::class, 'uploadStudentData'])->name('uploadStudentData');
    Route::get('/resetPasswordForm', [UserController::class, 'showResetPasswordForm'])->name('showResetPasswordForm');
    Route::resource('students', StudentsController::class);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // User management routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Admin Management Group
    Route::prefix('admin')->name('admin.')->group(function () {
        // Admin Management
        Route::resource('admins', AdminController::class)->names('management');

        // Audit Logs
        Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\AuditLogController::class, 'show'])->name('show');
            Route::delete('/{id}', [\App\Http\Controllers\AuditLogController::class, 'destroy'])->name('destroy');
            Route::post('/clear', [\App\Http\Controllers\AuditLogController::class, 'clearOldLogs'])->name('clear');
        });
    });

    Route::resource('classes', StudentClassesController::class);

    // Admin Management Routes (CRUD via modals in admin.blade.php)
    Route::resource('admins', AdminController::class);
    Route::post('/admins/bulk-action', [AdminController::class, 'bulkAction'])->name('admins.bulk-action');
    Route::post('/admins/{admin}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admins.toggle-status');
    Route::post('/admins/{admin}/force-password-reset', [AdminController::class, 'forcePasswordReset'])->name('admins.force-password-reset');

    Route::resource('teachers', TeachersController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('courses', CoursesController::class);
    Route::resource('academics', AcademicsController::class);
});

Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::get('/teacher/marks-entry', [TeacherMarksController::class, 'index'])->name('teacher.marks.entry');
    Route::resource('grades', TeacherMarksController::class);
    Route::resource('results', ResultsController::class)->only(['index']);
    Route::resource('allresults', AllResultsController::class);
});

