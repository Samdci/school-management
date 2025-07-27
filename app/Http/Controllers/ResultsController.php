<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentClasses;
use App\Models\Exam;
use App\Models\Term;
use App\Models\User;
use App\Models\ExamCourse;
use App\Models\Course;
use App\Models\Grade;

class ResultsController extends Controller
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

    public function index(Request $request)
    {
        $classes = StudentClasses::all();
        $exams = Exam::with('term')->get();
        $years = Term::select('term_year')->distinct()->orderBy('term_year', 'desc')->pluck('term_year');
    
        $students = collect();
        $courses = collect();
        $grades = [];
        $selectedClass = $request->class_id;
        $selectedExam = $request->exam_id;
        $selectedYear = $request->year;
    
        if ($selectedClass && $selectedExam && $selectedYear) {
            // Fetch students in the selected class
            $students = User::where('role', 'student')->where('student_class_id', $selectedClass)->get();
    
            // Ensure students exist
            if ($students->isEmpty()) {
                return redirect()->back()->with('error', 'No students found for the selected class.');
            }
    
            // Fetch courses for the selected exam (via exam_course)
            $examCourseIds = ExamCourse::where('exam_id', $selectedExam)->pluck('course_id');
            $courses = Course::whereIn('id', $examCourseIds)->get();
    
            // Ensure courses exist
            if ($courses->isEmpty()) {
                return redirect()->back()->with('error', 'No courses found for the selected exam.');
            }
    
            // Fetch grades for these students, courses, and exam
            $gradeRecords = Grade::whereIn('student_id', $students->pluck('id'))
                ->whereIn('course_id', $courses->pluck('id'))
                ->where('exam_id', $selectedExam)
                ->get();
    
            foreach ($gradeRecords as $grade) {
                $grades[$grade->student_id][$grade->course_id] = [
                    'marks' => $grade->marks,
                    'remarks' => $grade->remarks,
                ];
            }
        }
    
    
        return view('results', compact('classes', 'exams', 'years', 'students', 'courses', 'grades', 'selectedClass', 'selectedExam', 'selectedYear'));
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
            'year' => 'required|integer',
            'remarks' => 'array',
            'remarks.*' => 'nullable|string|max:255',
        ]);

        $classId = $request->class_id;
        $examId = $request->exam_id;
        $year = $request->year;
        $remarks = $request->input('remarks', []);

        // Get students in the selected class
        $students = User::where('role', 'student')->where('student_class_id', $classId)->get();

        // Get courses for the selected exam
        $examCourseIds = ExamCourse::where('exam_id', $examId)->pluck('course_id');
        $courses = Course::whereIn('id', $examCourseIds)->get();

        foreach ($students as $student) {
            foreach ($courses as $course) {
                // Find or create a grade record for the student, course, and exam
                $grade = Grade::firstOrNew([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'exam_id' => $examId,
                ]);

                // Update the remarks field
                $grade->remarks = $remarks[$student->id] ?? null;
                $grade->save();
            }
        }

        return redirect()->back()->with('success', 'Remarks updated successfully!');
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
