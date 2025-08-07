<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
        //
        $teachers = User::where('role', 'teacher')->get();
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
            'email' => 'required|email|unique:users,email',
            'phonenumber' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'home_county' => 'nullable|string|max:255',
            'student_class_id' => 'nullable',
        ]);

        $teacherclass = StudentClasses::where('id', $validated['student_class_id'])->select('class_name')->first();
        $defaultPassword = 123456789;

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phonenumber' => $validated['phonenumber'],
            'gender' => $validated['gender'],
            'home_county' => $validated['home_county'] ?? null,
            'class_name' => $teacherclass['class_name'] ?? null,
            'student_class_id' => $validated['student_class_id'] ?? null,
            'password' => bcrypt($defaultPassword),
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
        //
        $teacher = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'phonenumber' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'home_county' => 'nullable|string|max:255',
            'student_class_id'=>'nullable',

        ]);

        $teacherclass = StudentClasses::where('id', $validated['student_class_id'])->select('class_name')->first();

        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];
        $teacher->phonenumber = $validated['phonenumber'];
        $teacher->gender = $validated['gender'];
        $teacher->home_county = $validated['home_county'] ?? null;
        $teacher->class_name = $teacherclass['class_name'] ?? null;
        $teacher->student_class_id = $validated['student_class_id'] ?? null;
        $teacher->save();


        return redirect()->back()->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $teacher = User::findOrFail($id);
        $teacher->delete();
        return redirect()->back()->with('success', 'reacher deleted successfully!');
    }
}

