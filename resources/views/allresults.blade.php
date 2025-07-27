@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <h2>All Results</h2>

    <!-- Success and Error Messages -->
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
    <form method="GET" action="{{ route('allresults.index') }}">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label for="grade">Grade</label>
                <select name="grade" id="grade" class="form-control" required>
                    <option value="">Select Grade</option>
                    <option value="grade7" {{ request('grade') == 'grade7' ? 'selected' : '' }}>Grade 7</option>
                    <option value="grade8" {{ request('grade') == 'grade8' ? 'selected' : '' }}>Grade 8</option>
                    <option value="grade9" {{ request('grade') == 'grade9' ? 'selected' : '' }}>Grade 9</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="exam_id">Exam</label>
                <select name="exam_id" id="exam_id" class="form-control">
                    <option value="">Select Exam</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }} ({{ $exam->term->term_name ?? '' }} {{ $exam->term->term_year ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="year">Year</label>
                <select name="year" id="year" class="form-control">
                    <option value="">Select Year</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="margin-top: 6px; margin-bottom: 6px;">
            <button type="submit" class="btn btn-primary btn-sm mt-2 button">Filter Results</button>
        </div>
    </form>

    <!-- Results Table -->
    @if($students->count() && $courses->count())
    <div class="box mt-4">
        <div class="box-header">
            <h3 class="box-title">Students' Marks</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        @foreach($courses as $course)
                            <th>{{ $course->course_name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        @foreach($courses as $course)
                            <td>{{ $grades[$student->id][$course->id]['marks'] ?? '-' }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
        <p class="text-center mt-4">No results found. Please adjust the filters and try again.</p>
    @endif
</div>
@endsection