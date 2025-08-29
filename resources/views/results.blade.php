@extends('layouts.backend')
@section('content')
<div class="container-fluid">
    <h2>Remarks Entry</h2>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter Form -->
    <form method="GET" action="{{ route('results.index') }}">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label for="class_id">Class</label>
                <select name="class_id" id="class_id" class="form-control" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ (request('class_id', $selectedClass) == $class->id) ? 'selected' : '' }}>{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="exam_id">Exam</label>
                <select name="exam_id" id="exam_id" class="form-control" required>
                    <option value="">Select Exam</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ (request('exam_id', $selectedExam) == $exam->id) ? 'selected' : '' }}>{{ $exam->name }} ({{ $exam->term->term_name ?? '' }} {{ $exam->term->term_year ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="year">Year</label>
                <select name="year" id="year" class="form-control" required>
                    <option value="">Select Year</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ (request('year', $selectedYear) == $year) ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="margin-top: 6px; margin-bottom: 6px;">
            <button type="submit" class="btn btn-primary btn-sm mt-2 button">Show Results</button>
        </div>
    </form>

    <!-- Results and Remarks Entry -->
    @if($students->count() && $courses->count())
    <form method="POST" action="{{ route('results.store') }}">
        @csrf
        <input type="hidden" name="class_id" value="{{ $selectedClass }}">
        <input type="hidden" name="exam_id" value="{{ $selectedExam }}">
        <input type="hidden" name="year" value="{{ $selectedYear }}">
        <div class="box mt-4">
            <div class="box-header">
                <h3 class="box-title">Results Table</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            @foreach($courses as $course)
                                <th>{{ $course->course_name }}</th>
                            @endforeach
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            @foreach($courses as $course)
                                <td>
                                    {{ $grades[$student->id][$course->id]['marks'] ?? '-' }}
                                </td>
                            @endforeach
                            <td>
                                <input type="text" name="remarks[{{ $student->id }}]" class="form-control input-sm" value="{{ $grades[$student->id][$courses->first()->id]['remarks'] ?? '' }}" placeholder="Enter remarks">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success btn-sm mt-2 button">Save Remarks</button>
            </div>
        </div>


    </form>

    @endif


</div>
@endsection
