<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\StudentClasses;

class TeachersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $teachers = Teacher::with(['user', 'studentClass'])->get();
        $classes = StudentClasses::all();
        return view('teachers', compact('teachers', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phonenumber' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'homecounty' => 'nullable|string|max:255',
            'student_class_id' => 'nullable|exists:student_classes,id',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $defaultPassword = 123456789;

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($defaultPassword),
            'is_active' => true,
            'must_change_password' => true,
            'role_id' => 2, // Assuming 2 = teacher, adjust as needed
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'phonenumber' => $validated['phonenumber'],
            'gender' => $validated['gender'],
            'homecounty' => $validated['homecounty'] ?? null,
            'student_class_id' => $validated['student_class_id'] ?? null,
            'course_id' => $validated['course_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Teacher added successfully! Default password: ' . $defaultPassword);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $teacher->user->id,
            'email' => 'required|email|unique:users,email,' . $teacher->user->id,
            'phonenumber' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'homecounty' => 'nullable|string|max:255',
            'student_class_id' => 'nullable|exists:student_classes,id',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $teacher->user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        $teacher->update([
            'phonenumber' => $validated['phonenumber'],
            'gender' => $validated['gender'],
            'homecounty' => $validated['homecounty'] ?? null,
            'student_class_id' => $validated['student_class_id'] ?? null,
            'course_id' => $validated['course_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Teacher updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        if ($teacher->user) {
            $teacher->user->delete();
        }
        $teacher->delete();
        return redirect()->back()->with('success', 'Teacher deleted successfully!');
    }
}

