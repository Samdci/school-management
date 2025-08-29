<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherCourse;
use App\Models\Teacher;
use App\Models\Course;

class TeacherCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = TeacherCourse::with(['teacher.user', 'course']);
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        // Fix boolean filter: respect "0" value
        $filterIsPrimary = $request->query('is_primary', null);
        if ($filterIsPrimary !== null && $filterIsPrimary !== '') {
            $query->where('is_primary', (bool) intval($filterIsPrimary));
        }
        $assignments = $query->orderByDesc('id')->get();
        $teachers = Teacher::with('user')->orderBy('id')->get();
        $courses = Course::orderBy('course_name')->get();
        return view('teacher_courses.index', [
            'assignments' => $assignments,
            'teachers' => $teachers,
            'courses' => $courses,
            'filter_teacher_id' => $request->teacher_id,
            'filter_course_id' => $request->course_id,
            'filter_is_primary' => (string) $request->query('is_primary', ''),
        ]);
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
            'teacher_id' => 'required|exists:teachers,id',
            'course_id' => 'required|exists:courses,id',
            'is_primary' => 'nullable|boolean',
        ]);
        // Prevent duplicate assignment
        if (TeacherCourse::where('teacher_id', $validated['teacher_id'])->where('course_id', $validated['course_id'])->exists()) {
            return redirect()->back()->with('error', 'This teacher is already assigned to this course.');
        }
        TeacherCourse::create([
            'teacher_id' => $validated['teacher_id'],
            'course_id' => $validated['course_id'],
            'is_primary' => $request->boolean('is_primary'),
        ]);
        return redirect()->back()->with('success', 'Assignment added successfully!');
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
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'course_id' => 'required|exists:courses,id',
            'is_primary' => 'nullable|boolean',
        ]);
        $assignment = TeacherCourse::findOrFail($id);
        // Prevent duplicate assignment (excluding current record)
        if (TeacherCourse::where('teacher_id', $validated['teacher_id'])
            ->where('course_id', $validated['course_id'])
            ->where('id', '!=', $assignment->id)
            ->exists()) {
            return redirect()->back()->with('error', 'This teacher is already assigned to this course.');
        }
        $assignment->update([
            'teacher_id' => $validated['teacher_id'],
            'course_id' => $validated['course_id'],
            'is_primary' => $request->boolean('is_primary'),
        ]);
        return redirect()->back()->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assignment = TeacherCourse::findOrFail($id);
        $assignment->delete();
        return redirect()->back()->with('success', 'Assignment deleted successfully!');
    }
}
