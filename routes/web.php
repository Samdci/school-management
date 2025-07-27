<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;


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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*student routes*/
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::get('/download-file', [App\Http\Controllers\StudentsController::class, 'downloadfile'])->name('downloadfile');
    Route::post('/upload-student-data', [App\Http\Controllers\StudentsController::class, 'uploadStudentData'])->name('uploadStudentData');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/classes', [App\Http\Controllers\StudentClassesController::class, 'index'])->name('classes.index');
    Route::post('/classes', [App\Http\Controllers\StudentClassesController::class, 'store'])->name('classes.store');
    Route::put('/classes/{class}', [App\Http\Controllers\StudentClassesController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [App\Http\Controllers\StudentClassesController::class, 'destroy'])->name('classes.destroy');

    Route::resource('students', App\Http\Controllers\StudentsController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('teachers', App\Http\Controllers\TeachersController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('courses', App\Http\Controllers\CoursesController::class);
    Route::resource('academics', App\Http\Controllers\AcademicsController::class);
});

Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::get('/teacher/marks-entry', [App\Http\Controllers\TeacherMarksController::class, 'index'])->name('teacher.marks.entry');
    Route::resource('grades', App\Http\Controllers\TeacherMarksController::class);
    Route::resource('results', App\Http\Controllers\ResultsController::class);
    Route::resource('allresults', App\Http\Controllers\AllResultsController::class);
});

