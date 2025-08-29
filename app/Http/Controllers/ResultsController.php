<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentClasses;
use App\Models\Exam;
use App\Models\Term;
use App\Models\Student;
use App\Models\ExamCourse;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\TeacherCourse;

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
        $classes = StudentClasses::orderBy('class_name')->get();
        $exams = Exam::with('term')->orderBy('id', 'desc')->get();
        $years = Term::select('term_year')->distinct()->orderBy('term_year', 'desc')->pluck('term_year');
        $categories = collect(['grade7', 'grade8', 'grade9']);

        $students = collect();
        $courses = collect();
        $grades = [];
        $overall = [];
        $courseMeans = [];
        $overallMean = null;
        $overallMeanGrade = null;

        $selectedClass = $request->class_id;
        $selectedCategory = $request->category;
        $selectedExam = $request->exam_id;
        $selectedYear = $request->year;
        $selectedTeacher = $request->teacher_id;

        // Determine teachers eligible for the selected exam (assigned to any of the exam's courses)
        $teachers = collect();
        $examCourseIds = collect();
        if ($selectedExam) {
            $examCourseIds = ExamCourse::where('exam_id', $selectedExam)->pluck('course_id');
            $teacherIds = TeacherCourse::whereIn('course_id', $examCourseIds)->pluck('teacher_id')->unique();
            $teachers = Teacher::with('user')->whereIn('id', $teacherIds)->orderBy('id')->get();
        } else {
            // If no exam selected, list all teachers for convenience
            $teachers = Teacher::with('user')->orderBy('id')->get();
        }

        // Build students set using Student model
        if ($selectedClass) {
            $students = Student::where('student_class_id', $selectedClass)
                ->orderBy('name')
                ->get();
        } elseif ($selectedCategory) {
            $classIdsInCategory = StudentClasses::where('category', $selectedCategory)->pluck('id');
            $students = Student::whereIn('student_class_id', $classIdsInCategory)
                ->orderBy('name')
                ->get();
        }

        // Courses from exam; optionally restricted by teacher assignment
        if ($selectedExam) {
            $coursesQuery = Course::whereIn('id', $examCourseIds);
            if ($selectedTeacher) {
                $teacherCourseIds = TeacherCourse::where('teacher_id', $selectedTeacher)->pluck('course_id');
                $coursesQuery->whereIn('id', $teacherCourseIds);
            }
            $courses = $coursesQuery->orderBy('course_name')->get();
        }

        // Load grades when we have all core filters
        if ($students->isNotEmpty() && $courses->isNotEmpty() && $selectedExam && $selectedYear) {
            $gradeRecords = Grade::whereIn('student_id', $students->pluck('id'))
                ->whereIn('course_id', $courses->pluck('id'))
                ->where('exam_id', $selectedExam)
                ->whereHas('exam.term', function ($termQuery) use ($selectedYear) {
                    $termQuery->where('term_year', $selectedYear);
                })
                ->get();

            foreach ($gradeRecords as $grade) {
                $grades[$grade->student_id][$grade->course_id] = [
                    'marks' => $grade->marks,
                    'grade' => $grade->grade,
                    'remarks' => $grade->remarks,
                ];
            }

            // Compute overall average and grade per student
            $studentAverages = [];
            foreach ($students as $student) {
                $sum = 0; $count = 0;
                foreach ($courses as $course) {
                    $mark = $grades[$student->id][$course->id]['marks'] ?? null;
                    if (!is_null($mark)) { $sum += $mark; $count++; }
                }
                $avg = $count ? round($sum / $count, 2) : null;
                $overall[$student->id] = [
                    'average' => $avg,
                    'overall_grade' => !is_null($avg) ? $this->calculateGrade($avg) : null,
                ];
                if (!is_null($avg)) {
                    $studentAverages[] = $avg;
                }
            }

            // Compute per-course means
            foreach ($courses as $course) {
                $sum = 0; $count = 0;
                foreach ($students as $student) {
                    $mark = $grades[$student->id][$course->id]['marks'] ?? null;
                    if (!is_null($mark)) { $sum += $mark; $count++; }
                }
                $avg = $count ? round($sum / $count, 2) : null;
                $courseMeans[$course->id] = [
                    'average' => $avg,
                    'grade' => !is_null($avg) ? $this->calculateGrade($avg) : null,
                ];
            }

            // Compute overall mean of student averages and its grade
            if (!empty($studentAverages)) {
                $overallMean = round(array_sum($studentAverages) / count($studentAverages), 2);
                $overallMeanGrade = $this->calculateGrade($overallMean);
            }
        }

        return view('results.index', compact(
            'classes', 'exams', 'years', 'categories', 'teachers',
            'students', 'courses', 'grades', 'overall', 'courseMeans', 'overallMean', 'overallMeanGrade',
            'selectedClass', 'selectedCategory', 'selectedExam', 'selectedYear', 'selectedTeacher'
        ));
    }

    /**
     * Map average marks to overall grade (same thresholds as TeacherMarksController)
     */
    protected function calculateGrade($marks)
    {
        if ($marks >= 75) return 'Exceeding Expectation';
        if ($marks >= 60) return 'Meeting Expectation';
        if ($marks >= 40) return 'Approaching Expectation';
        return 'Below Expectation';
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
        // No-op: remarks are auto-generated in grade entry; keeping method for route compatibility
        return redirect()->back();
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
