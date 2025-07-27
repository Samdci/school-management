<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentClasses;

class StudentClassesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $classes = StudentClasses::all();
        return view('classes', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'category' => 'required|in:grade7,grade8,grade9',
        ]);
        StudentClasses::create($validated);
        return redirect()->back()->with('success', 'Class added successfully!');
    }

    public function update(Request $request, $id)
    {
        $class = StudentClasses::findOrFail($id);
        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'category' => 'required|in:grade7,grade8,grade9',
        ]);
        $class->update($validated);
        return redirect()->back()->with('success', 'Class updated successfully!');
    }

    public function destroy($id)
    {
        $class = StudentClasses::findOrFail($id);
        $class->delete();
        return redirect()->back()->with('success', 'Class deleted successfully!');
    }
}
