@extends('layouts.backend')
@section('content')
<div class="container-fluid">
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
    <h2>Enter Grades</h2>
    <form method="GET" action="{{ route('grades.index') }}">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label for="class_id">Class</label>
                <select name="class_id" id="class_id" class="form-control" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ (request('class_id') == $class->id) ? 'selected' : '' }}>{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="exam_id">Exam</label>
                <select name="exam_id" id="exam_id" class="form-control" required>
                    <option value="">Select Exam</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ (request('exam_id') == $exam->id) ? 'selected' : '' }}>{{ $exam->name }} ({{ $exam->term->term_name ?? '' }} {{ $exam->term->term_year ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="year">Year</label>
                <select name="year" id="year" class="form-control" required>
                    <option value="">Select Year</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ (request('year') == $year) ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($userRole === 'teacher' && $courses->count())
        <div class="row">
            <div class="col-md-4 mb-2">
                <label for="course_id">Course</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (request('course_id') == $course->id) ? 'selected' : '' }}>{{ $course->course_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
        <div style="margin-top: 3px;">
            <button type="submit" class="btn btn-primary mt-2 button">Next</button>
        </div>
    </form>

    <pre>
    userRole: {{ $userRole }}
    selectedClass: {{ $selectedClass }}
    selectedExam: {{ $selectedExam }}
    selectedYear: {{ $selectedYear }}
    selectedCourse: {{ $selectedCourse }}
    courses: {{ $courses->pluck('id') }}
    students: {{ $students->count() }}
    </pre>

    @if($students->count() && $courses->count() && ($userRole !== 'teacher' || $selectedCourse))
        <div class="box mt-4">
            <div class="box-header d-flex justify-content-between align-items-center">
                <h3 class="box-title">Enter Marks for Selected Class & Exam</h3>
                <!-- Bulk Upload Form -->
                <form action="{{ route('grades.store') }}" method="POST" enctype="multipart/form-data" style="display:inline-block;">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                    <input type="hidden" name="exam_id" value="{{ $selectedExam }}">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    @if($userRole === 'teacher')
                        <input type="hidden" name="course_id" value="{{ $selectedCourse }}">
                    @endif
                    <input type="file" name="marks_file" required>
                    <div style="margin-top: 3px;">
                        <button type="button" class="btn btn-primary button" data-toggle="modal" data-target="#bulkUploadModal">
                            Bulk Upload
                        </button>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <form action="{{ route('grades.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                    <input type="hidden" name="exam_id" value="{{ $selectedExam }}">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    @if($userRole === 'teacher')
                        <input type="hidden" name="course_id" value="{{ $selectedCourse }}">
                    @endif
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                @foreach($courses as $course)
                                    @if($userRole !== 'teacher' || $selectedCourse == $course->id)
                                        <th>{{ $course->course_name }}</th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                @foreach($courses as $course)
                                    @if($userRole !== 'teacher' || $selectedCourse == $course->id)
                                    <td>
                                        <input type="number" name="marks[{{ $student->id }}][{{ $course->id }}]" class="form-control input-sm" min="0" max="100" placeholder="Mark">
                                        <input type="hidden" name="teacher_id" value="{{ $teacherId }}">
                                    </td>
                                    @endif
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary button">Save Marks</button>
                </form>
            </div>
        </div>
    @endif

    <!-- Bulk upload Modal -->
<div class="modal fade" id="BulkUploadModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span style="color:#dc3545;"><center><h4>Upload Students</h4></center></span>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Select file to upload</label>
                                <input type="file" class="form-control" name="file">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <span style="color:#dc3545;" class="pull-right"><p>DOWNLOAD UPLOAD FILE:</p></span>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('downloadfile') }}">
                                <button type="button" class="btn btn-sm btn-success button">Download</button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left button" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm button">Upload students</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end of upload Modal -->
</div>
@endsection
