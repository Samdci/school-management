<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalUsers = User::count();
        $students = Student::all();
        $allStudents = $students->count();
        $teachers = Teacher::all();
        $allTeachers = $teachers->count();
        return view('home', compact('totalUsers', 'allStudents', 'allTeachers'));
    }
}
