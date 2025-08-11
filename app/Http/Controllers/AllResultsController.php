<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Term;
use App\Models\User;
use App\Models\ExamCourse;
use App\Models\Course;
use App\Models\Grade;

class AllResultsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

public function index(Request $request)
{
    // Fetch all exams and years for the filter dropdowns
    $exams = Exam::with('term')->get();
    $years = Term::select('term_year')->distinct()->orderBy('term_year', 'desc')->pluck('term_year');

    // Initialize empty collections and variables
    $students = collect();
    $courses = collect();
    $grades = [];
    $selectedGrade = $request->grade;
    $selectedExam = $request->exam_id;
    $selectedYear = $request->year;

    // Check if filters are applied
    if ($selectedGrade || $selectedExam || $selectedYear) {
        // Fetch students based on the selected grade using role relationship
        $students = User::whereHas('role', function ($q) {
                $q->where('name', 'student');
            })
            ->whereHas('studentClass', function ($query) use ($selectedGrade) {
                if ($selectedGrade) {
                    $query->where('category', $selectedGrade);
                }
            })
            ->with(['student', 'studentClass', 'role'])
            ->get();

        // Ensure students exist
        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'No students found for the selected grade.');
        }

        // Fetch courses for the selected exam
        $examCourseIds = ExamCourse::where('exam_id', $selectedExam)->pluck('course_id');
        $courses = Course::whereIn('id', $examCourseIds)->get();

        // Ensure courses exist
        if ($courses->isEmpty()) {
            return redirect()->back()->with('error', 'No courses found for the selected exam.');
        }

        // Fetch grades for the selected students, courses, and exam
        $gradeRecords = Grade::whereIn('student_id', $students->pluck('id'))
            ->whereIn('course_id', $courses->pluck('id'))
            ->when($selectedExam, function ($query) use ($selectedExam) {
                $query->where('exam_id', $selectedExam);
            })
            ->when($selectedYear, function ($query) use ($selectedYear) {
                $query->whereHas('exam.term', function ($termQuery) use ($selectedYear) {
                    $termQuery->where('term_year', $selectedYear);
                });
            })
            ->get();

        // Map grades to the students and courses
        foreach ($gradeRecords as $grade) {
            $grades[$grade->student_id][$grade->course_id] = [
                'marks' => $grade->marks,
            ];
        }
    }

    // Return the view with the data
    return view('allresults', compact('exams', 'years', 'students', 'courses', 'grades', 'selectedGrade', 'selectedExam', 'selectedYear'));
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
        //
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
