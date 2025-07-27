<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\Course;

class AcademicsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $terms = Term::all();
        $exams = Exam::with('term')->get();
        $examCourses = ExamCourse::with(['exam', 'course'])->get();

        return view('academics', compact('terms', 'exams', 'examCourses'));
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
        // Add Term
        if ($request->has('term_name') && $request->has('term_year')) {
            $validated = $request->validate([
                'term_name' => 'required|string',
                'term_year' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'status' => 'required|in:ongoing,complete',
            ]);
            Term::create($validated);
            return redirect()->back()->with('success', 'Term added successfully!');
        }
        // Add Exam
        if ($request->has('name') && $request->has('term_id')) {
            $validated = $request->validate([
                'name' => 'required|in:Opener,Midterm,End term',
                'term_id' => 'required|exists:terms,id',
            ]);
            Exam::create($validated);
            return redirect()->back()->with('success', 'Exam added successfully!');
        }
        // Add Exam Course
        if ($request->has('exam_id') && $request->has('course_id')) {
            $validated = $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'course_id' => 'required|exists:courses,id',
            ]);
            \App\Models\ExamCourse::create($validated);
            return redirect()->back()->with('success', 'Exam course added successfully!');
        }
        return redirect()->back()->with('error', 'Invalid data submitted.');
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
        // Update Term
        if ($request->has('term_name') && $request->has('term_year')) {
            $validated = $request->validate([
                'term_name' => 'required|string',
                'term_year' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'status' => 'required|in:ongoing,complete',
            ]);
            $term = Term::findOrFail($id);
            $term->update($validated);
            return redirect()->back()->with('success', 'Term updated successfully!');
        }
        // Update Exam
        if ($request->has('name') && $request->has('term_id')) {
            $validated = $request->validate([
                'name' => 'required|in:Opener,Midterm,End term',
                'term_id' => 'required|exists:terms,id',
            ]);
            $exam = Exam::findOrFail($id);
            $exam->update($validated);
            return redirect()->back()->with('success', 'Exam updated successfully!');
        }
        // Update Exam Course
        if ($request->has('exam_id') && $request->has('course_id')) {
            $validated = $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'course_id' => 'required|exists:courses,id',
            ]);
            $examCourse = \App\Models\ExamCourse::findOrFail($id);
            $examCourse->update($validated);
            return redirect()->back()->with('success', 'Exam course updated successfully!');
        }
        return redirect()->back()->with('error', 'Invalid data submitted.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Try to delete as Term, Exam, or ExamCourse
        if ($term = Term::find($id)) {
            $term->delete();
            return redirect()->back()->with('success', 'Term deleted successfully!');
        }
        if ($exam = Exam::find($id)) {
            $exam->delete();
            return redirect()->back()->with('success', 'Exam deleted successfully!');
        }
        if ($examCourse = \App\Models\ExamCourse::find($id)) {
            $examCourse->delete();
            return redirect()->back()->with('success', 'Exam course deleted successfully!');
        }
        return redirect()->back()->with('error', 'Item not found.');
    }
}
