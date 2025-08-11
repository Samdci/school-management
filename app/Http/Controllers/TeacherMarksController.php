<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentClasses;
use App\Models\Exam;
use App\Models\Term;

class TeacherMarksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $classes = StudentClasses::all();
        // Fetch all exams with their terms
        $exams = Exam::with('term')->get();
        // Fetch all years from terms table (distinct)
        $years = Term::select('term_year')->distinct()->orderBy('term_year', 'desc')->pluck('term_year');

        $students = collect();
        $courses = collect();
        $selectedClass = $request->class_id;
        $selectedExam = $request->exam_id;
        $selectedYear = $request->year;
        $selectedCourse = $request->course_id;
        if ($selectedCourse) {
            $selectedCourse = (int) $selectedCourse;
        }
        $teacherId = auth()->id();
        $userRole = auth()->user()->role ?? null;

        if ($selectedClass && $selectedExam && $selectedYear) {
            // Fetch students in the selected class using User model with role relationship
            $students = \App\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'student');
            })->where('student_class_id', $selectedClass)->with(['student', 'studentClass', 'role'])->get();
            // Fetch courses for the selected exam (via exam_course)
            $examCourseIds = \App\Models\ExamCourse::where('exam_id', $selectedExam)->pluck('course_id');
            $allCourses = \App\Models\Course::whereIn('id', $examCourseIds)->get();

            if ($userRole === 'teacher') {
                // If a course is selected, show only that course
                if ($selectedCourse) {
                    $courses = $allCourses->where('id', $selectedCourse);
                } else {
                    $courses = $allCourses;
                }
            } else {
                // Admin: show all courses
                $courses = $allCourses;
            }
        }

        return view('grades', compact('classes', 'exams', 'years', 'students', 'courses', 'selectedClass', 'selectedExam', 'selectedYear', 'selectedCourse', 'teacherId', 'userRole'));
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
        $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'exam_id' => 'required|exists:exams,id',
            'year' => 'required',
            'marks' => 'array',
        ]);

        $marks = $request->input('marks', []);
        $examId = $request->exam_id;

        foreach ($marks as $studentId => $courses) {
            foreach ($courses as $courseId => $mark) {
                $grade = \App\Models\Grade::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'exam_id' => $examId,
                    ],
                    [
                        'marks' => $mark,
                    ]
                );
            }
        }

        if($grade){
            return redirect()->back()->with('success', 'Student marks uploaded successfully!');
        }
        else{
            return redirect()->back()->with('error', 'Student marks upload failed!');
        }
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
    }
}
