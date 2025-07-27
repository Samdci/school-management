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
            // Fetch students in the selected class
            $students = \App\Models\User::where('role', 'student')->where('student_class_id', $selectedClass)->get();
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

     public function gradeUpload(Request $request)
    {
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach($rows as $index => $row){
            if (empty($row)) continue;
            // Skip the first row (header)
            if($index == 0){
                continue;
            }

            $defaultPassword = 123456789;

            if(count($row) >= 3 && !empty($row[0])){
                // Look up class by class_name
                $class = StudentClasses::where('class_name', $row[11])->first();
                $student_class_id = $class ? $class->id : null;
                $user = User::create([
                    'name' => $row[0] ,
                    'email' => $row[2] ?? null,
                    'phonenumber' => $row[3] ?? null,
                    'gender' => $row[1] ?? null,
                    'role' => 'student',
                    'guardian_fullname' => $row[5] ?? null,
                    'guardian_relationship' => $row[6] ?? null,
                    'guardian_phonenumber' => $row[7] ?? null,
                    'home_county' => $row[8] ?? null,
                    'kcpe_marks' => $row[9] ?? null,
                    'cert_copy' => $row[10] ?? null,
                    'class_name' => $row[11] ?? null,
                    'student_class_id' => $student_class_id,
                    'password' => bcrypt($defaultPassword),
                    'must_change_password' => true,
                ]);

                if($user){
                    return redirect()->back()->with('success', 'Student data uploaded successfully!');
                }
                else{
                    return redirect()->back()->with('error', 'Student data upload failed!');
                }
            }
        }

        return redirect()->back();
    }

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
        $teacherId = auth()->id();

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
                        'teacher_id' => $teacherId,
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
