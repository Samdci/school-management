<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentClassesController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\AcademicsController;
use App\Http\Controllers\TeacherMarksController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\AllResultsController;
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

/*student routes*/
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::get('/download-file', [StudentsController::class, 'downloadfile'])->name('downloadfile');
    Route::post('/upload-student-data', [StudentsController::class, 'uploadStudentData'])->name('uploadStudentData');
    Route::get('/resetPasswordForm', [UserController::class, 'showResetPasswordForm'])->name('showResetPasswordForm');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/classes', [StudentClassesController::class, 'index'])->name('classes.index');
    Route::post('/classes', [StudentClassesController::class, 'store'])->name('classes.store');
    Route::put('/classes/{class}', [StudentClassesController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [StudentClassesController::class, 'destroy'])->name('classes.destroy');

    Route::resource('students', StudentsController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('teachers', TeachersController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('courses', CoursesController::class);
    Route::resource('academics', AcademicsController::class);
});

Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::get('/teacher/marks-entry', [TeacherMarksController::class, 'index'])->name('teacher.marks.entry');
    Route::resource('grades', TeacherMarksController::class);
    Route::resource('results', ResultsController::class);
    Route::resource('allresults', AllResultsController::class);
});

