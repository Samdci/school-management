<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentClasses;
use App\Models\Exam;
use App\Models\Term;
use App\Models\Student;
use App\Models\Course;
use App\Models\ExamCourse;
use App\Models\Grade;
use App\Models\TeacherCourse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherMarksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Display the grade entry form with step-by-step workflow.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    /**
     * Load existing grades for students in the selected exam and course.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $students
     * @param  int  $examId
     * @param  int|null  $courseId
     * @return void
     */
    protected function loadExistingGrades($students, $examId, $courseId = null)
    {
        $studentIds = $students->pluck('id')->toArray();

        $query = Grade::whereIn('student_id', $studentIds)
            ->where('exam_id', $examId)
            ->with('course');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $grades = $query->get();

        // Map grades to students
        $students->each(function ($student) use ($grades, $courseId) {
            if ($courseId) {
                // For single course view
                $grade = $grades->where('student_id', $student->id)->first();
                $student->grade = $grade;
            } else {
                // For all courses view (admin)
                $student->grades = $grades->where('student_id', $student->id)->keyBy('course_id');
            }
        });
    }

    /**
     * Display the grade entry form with step-by-step workflow.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            // Enable error reporting for debugging
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            $user = auth()->user();
            // Fix: detect teacher role via relation
            $isTeacher = $user && $user->role && ($user->role->name === 'teacher');
            // Fix: teacherId should be Teacher model id, not User id
            $teacherModelId = $user && $user->teacher ? $user->teacher->id : null;

            // Get base data needed for the form
            $classes = StudentClasses::orderBy('class_name')->get();
            $terms = Term::orderBy('term_year', 'desc')
                ->orderBy('term_name', 'desc')
                ->get();

            $years = $terms->pluck('term_year')->unique()->sortDesc()->values();
            $exams = Exam::with('term')->orderBy('name')->get();

            // Initialize variables
            $students = collect();
            $courses = collect();
            $selectedClass = $request->class_id;
            $selectedExam = $request->exam_id;
            $selectedYear = $request->year;
            $selectedCourse = $request->course_id;

            // Determine current step
            $step = $request->step ?? 'filter';

            // Process form submission
            if ($selectedClass && $selectedExam && $selectedYear) {
                // Get the selected exam with its term
                $exam = Exam::with('term')->find($selectedExam);
                if (!$exam) {
                    return redirect()->route('grades.index')
                        ->with('error', 'Selected exam not found.');
                }

                // Get all students in the selected class
                $students = Student::where('student_class_id', $selectedClass)
                    ->orderBy('name')
                    ->get();

                // Get all courses for the selected exam
                $examCourseIds = ExamCourse::where('exam_id', $selectedExam)
                    ->pluck('course_id');
                $allCourses = Course::whereIn('id', $examCourseIds)->get();

                if ($isTeacher) {
                    // Teacher workflow
                    // Fix: query TeacherCourse with teacher model id
                    $teacherCourseIds = $teacherModelId
                        ? TeacherCourse::where('teacher_id', $teacherModelId)
                            ->whereIn('course_id', $examCourseIds)
                            ->pluck('course_id')
                        : collect();

                    $courses = $allCourses->whereIn('id', $teacherCourseIds)->values();

                    // Handle teacher steps
                    if ($step === 'filter') {
                        // If no courses are assigned, show error
                        if ($courses->isEmpty()) {
                            return redirect()->back()
                                ->with('error', 'You are not assigned to any courses for this exam.');
                        }
                        $step = 'select-course';
                    } elseif ($step === 'select-course') {
                        if ($selectedCourse) {
                            // Verify the teacher is assigned to this course
                            if (!$courses->contains('id', (int)$selectedCourse)) {
                                return redirect()->back()
                                    ->with('error', 'You are not authorized to access this course.');
                            }
                            $step = 'enter-marks';
                            $this->loadExistingGrades($students, $selectedExam, $selectedCourse);
                        } else {
                            // Stay on course selection until a course is chosen
                            $step = 'select-course';
                        }
                    }
                } else {
                    // Admin workflow
                    $courses = $allCourses;
                    $step = 'enter-marks';
                    $this->loadExistingGrades($students, $selectedExam);
                }
            }

            \Log::info('Grade Entry Debug', [
                'step' => $step,
                'isTeacher' => $isTeacher,
                'selectedCourse' => $selectedCourse,
                'teacherCourses' => $isTeacher ? ($courses ? $courses->pluck('id') : '[]') : 'N/A'
            ]);

            return view('grades.index', compact(
                'classes', 'terms', 'years', 'exams', 'students', 'courses',
                'selectedClass', 'selectedExam', 'selectedYear', 'selectedCourse',
                'step', 'isTeacher'
            ));

        } catch (\Throwable $e) {
            return response('<pre style="color:red;white-space:pre-wrap;">'.e($e->getMessage())."\n\n".e($e->getTraceAsString()).'</pre>', 500);
        }
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
     * Store grades for students in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'class_id' => 'required|exists:student_classes,id',
                'marks' => 'required|array',
                'marks.*' => 'array',
                'marks.*.*' => 'nullable|numeric|min:0|max:100',
            ]);

            $examId = $request->exam_id;
            $classId = $request->class_id;
            $courseId = $request->course_id;
            $marks = $request->marks;
            // Fix: detect teacher role via relation
            $user = auth()->user();
            $isTeacher = $user && $user->role && ($user->role->name === 'teacher');

            DB::beginTransaction();

            // Get all students in the class to validate against
            $studentIds = Student::where('student_class_id', $classId)->pluck('id')->toArray();

            // If teacher, validate they can only submit marks for their assigned courses
            if ($isTeacher && $courseId) {
                $teacherModelId = $user && $user->teacher ? $user->teacher->id : null;
                $validCourse = $teacherModelId ? TeacherCourse::where('teacher_id', $teacherModelId)
                    ->where('course_id', $courseId)
                    ->exists() : false;

                if (!$validCourse) {
                    throw new \Exception('You are not authorized to submit grades for this course.');
                }
            }

            // Get the exam to validate the academic period
            $exam = Exam::findOrFail($examId);
            $currentYear = $exam->term->term_year;
            $currentTerm = $exam->term_id;

            $gradesData = [];
            $now = now();

            foreach ($marks as $studentId => $courseMarks) {
                // Validate student is in the class
                if (!in_array($studentId, $studentIds)) {
                    throw new \Exception("Invalid student ID: {$studentId}");
                }

                foreach ($courseMarks as $courseId => $mark) {
                    // Skip if no mark provided
                    if ($mark === null || $mark === '') {
                        continue;
                    }

                    // For teachers, only allow submitting for their assigned course
                    if ($isTeacher && $courseId != $request->course_id) {
                        continue;
                    }

                    // Validate course exists in the exam
                    $validCourse = ExamCourse::where('exam_id', $examId)
                        ->where('course_id', $courseId)
                        ->exists();

                    if (!$validCourse) {
                        throw new \Exception("Invalid course ID: {$courseId} for this exam");
                    }

                    // Calculate grade based on marks
                    $grade = $this->calculateGrade($mark);

                    $gradesData[] = [
                        'student_id' => $studentId,
                        'exam_id' => $examId,
                        'course_id' => $courseId,
                        'marks' => $mark,
                        'grade' => $grade,
                        'remarks' => $this->getRemarks($grade),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // Delete existing grades for this exam/class (or course if teacher)
            $deleteQuery = Grade::where('exam_id', $examId)
                ->whereIn('student_id', $studentIds);

            if ($isTeacher && $courseId) {
                $deleteQuery->where('course_id', $courseId);
            }

            $deleteQuery->delete();

            // Insert new grades
            if (!empty($gradesData)) {
                Grade::insert($gradesData);
            }

            DB::commit();

            return redirect()
                ->route('grades.index', [
                    'class_id' => $classId,
                    'exam_id' => $examId,
                    'year' => $request->year,
                    'course_id' => $courseId,
                    'step' => $isTeacher ? 'enter-marks' : 'filter'
                ])
                ->with('success', 'Grades saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Grade submission failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error saving grades: ' . $e->getMessage());
        }
    }

    /**
     * Calculate grade based on marks
     */
    protected function calculateGrade($marks)
    {
        if ($marks >= 75) return 'Exceeding Expectation';
        if ($marks >= 60) return 'Meeting Expectation';
        if ($marks >= 40) return 'Approaching Expectation';
        return 'Below Expectation';
    }

    /**
     * Get remarks based on grade
     */
    protected function getRemarks($grade)
    {
        $remarks = [
            'Exceeding Expectation' => 'Excellent Pass',
            'Meeting Expectation' => 'Good Pass',
            'Approaching Expectation' => 'Pass',
            'Below Expectation' => 'Failed',
        ];

        return $remarks[$grade] ?? 'Pending';
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
