<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentClasses;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentsController extends Controller
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
        // Fetch all students with their class relationship
        $students = Student::with('studentClass')->get();
        $classes = StudentClasses::all();
        return view('allstudents', compact('students', 'classes'));
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
            'email' => 'required|email|unique:students,email',
            'gender' => 'required|in:male,female,other',
            'student_class_id' => 'nullable|exists:student_classes,id',
            'guardian_fullname' => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:255',
            'guardian_phonenumber' => 'nullable|string|max:50',
            'guardian_email' => 'nullable|email|max:255',
            'home_county' => 'nullable|string|max:255',
            'kcpe_marks' => 'nullable|string|max:255',
            'cert_number' => 'nullable|string|max:255',
        ]);

        // Create student record in students table
        $student = Student::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'student_class_id' => $validated['student_class_id'] ?? null,
            'guardian_fullname' => $validated['guardian_fullname'] ?? null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
            'guardian_phonenumber' => $validated['guardian_phonenumber'] ?? null,
            'guardian_email' => $validated['guardian_email'] ?? null,
            'home_county' => $validated['home_county'] ?? null,
            'kcpe_marks' => $validated['kcpe_marks'] ?? null,
            'cert_number' => $validated['cert_number'] ?? null,
        ]);


        if ($student){
            return redirect()->back()->with('success', 'Student added successfully!');
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
        $student = Student::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'gender' => 'required|in:male,female,other',
            'student_class_id' => 'nullable|exists:student_classes,id',
            'guardian_fullname' => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:255',
            'guardian_phonenumber' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'home_county' => 'nullable|string|max:255',
            'kcpe_marks' => 'nullable|string|max:255',
            'cert_number' => 'nullable|string|max:255',
        ]);

        $student->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'student_class_id' => $validated['student_class_id'] ?? null,
            'guardian_fullname' => $validated['guardian_fullname'] ?? null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
            'guardian_phonenumber' => $validated['guardian_phonenumber'] ?? null,
            'guardian_email' => $validated['guardian_email'] ?? null,
            'home_county' => $validated['home_county'] ?? null,
            'kcpe_marks' => $validated['kcpe_marks'] ?? null,
            'cert_number' => $validated['cert_number'] ?? null,
        ]);

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
        $student = Student::findOrFail($id);
        $student->delete();
        return redirect()->back()->with('success', 'Student deleted successfully!');
    }

    public function downloadfile()
    {
        $filePath= public_path('download/student/student_data4.xlsx');
        $fileName= 'student_data4.xlsx';
        if(file_exists($filePath)){
            return Response::download($filePath, $fileName);
        }
        abort(404, 'File does not exist!');
    }

    public function uploadStudentData(Request $request)
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

            if(count($row) >= 3 && !empty($row[0])){
                // Look up class by class_name if needed
                $student_class_id = null;
                if (isset($row[11])) {
                    $class = StudentClasses::where('class_name', $row[11])->first();
                    $student_class_id = $class ? $class->id : null;
                }

                // Create student record directly
                $student = Student::create([
                    'name' => $row[0] ?? '',
                    'email' => $row[1] ?? null,
                    'gender' => $row[2] ?? 'other',
                    'guardian_fullname' => $row[3] ?? null,
                    'guardian_relationship' => $row[4] ?? null,
                    'guardian_phonenumber' => $row[5] ?? null,
                    'guardian_email' => $row[6] ?? null,
                    'home_county' => $row[7] ?? null,
                    'kcpe_marks' => $row[8] ?? null,
                    'cert_number' => $row[9] ?? null,
                    'student_class_id' => $student_class_id,
                ]);
                
                if(!$student){
                    return redirect()->back()->with('error', 'Failed to create student record.');
                }
            }
        }

        return redirect()->back();
    }


}
