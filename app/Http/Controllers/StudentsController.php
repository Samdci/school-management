<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
         // Fetch all users with role 'student'
         $students = User::where('role', 'student')->get();

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
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phonenumber' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'student_class_id'=>'nullable',
            'guardian_fullname' => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:255',
            'guardian_phonenumber' => 'nullable|string|max:20',
            'home_county' => 'nullable|string|max:255',
            'kcpe_marks' => 'nullable|string|max:255',
            'cert_copy' => 'nullable|string|max:255',


        ]);

        $studentclass = StudentClasses::where('id', $validated['student_class_id'])->select('class_name')->first();
        // Generate a default password
        $defaultPassword = 123456789;

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phonenumber' => $validated['phonenumber'],
            'gender' => $validated['gender'],
            'class_name' => $studentclass['class_name'] ?? null,
            'student_class_id'=> $validated['student_class_id'] ?? null,
            'password' => bcrypt($defaultPassword),
            'guardian_fullname' => $validated['guardian_fullname'] ?? null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
            'guardian_phonenumber' => $validated['guardian_phonenumber'] ?? null,
            'home_county' => $validated['home_county'] ?? null,
            'kcpe_marks' => $validated['kcpe_marks'] ?? null,
            'cert_copy' => $validated['cert_copy'] ?? null,
            'role' => 'student', // Set role to student

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
        $student = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'phonenumber' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'guardian_fullname' => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:255',
            'guardian_phonenumber' => 'nullable|string|max:20',
            'home_county' => 'nullable|string|max:255',
            'kcpe_marks' => 'nullable|string|max:255',
            'cert_copy' => 'nullable|string|max:255',
            'student_class_id'=>'nullable',

        ]);

        $studentclass = StudentClasses::where('id', $validated['student_class_id'])->select('class_name')->first();

        $student->name = $validated['name'];
        $student->email = $validated['email'];
        $student->phonenumber = $validated['phonenumber'];
        $student->gender = $validated['gender'];
        $student->guardian_fullname = $validated['guardian_fullname'] ?? null;
        $student->guardian_relationship = $validated['guardian_relationship'] ?? null;
        $student->guardian_phonenumber = $validated['guardian_phonenumber'] ?? null;
        $student->home_county = $validated['home_county'] ?? null;
        $student->kcpe_marks = $validated['kcpe_marks'] ?? null;
        $student->cert_copy = $validated['cert_copy'] ?? null;
        $student->class_name = $studentclass['class_name'] ?? null;
        $student->student_class_id = $validated['student_class_id'] ?? null;
        $student->save();


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
        $student = User::findOrFail($id);
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


}
