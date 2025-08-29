@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <h2>Results</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label>Class</label>
            <select name="class_id" class="form-control">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" @if(isset($selectedClass) && $selectedClass==$class->id) selected @endif>{{ $class->class_name }} ({{ strtoupper($class->category) }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Category</label>
            <select name="category" class="form-control">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @if(isset($selectedCategory) && $selectedCategory==$category) selected @endif>{{ strtoupper($category) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Exam</label>
            <select name="exam_id" class="form-control">
                <option value="">Select Exam</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" @if(isset($selectedExam) && $selectedExam==$exam->id) selected @endif>{{ $exam->name }} ({{ $exam->term->term_name ?? '' }} {{ $exam->term->term_year ?? '' }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Year</label>
            <select name="year" class="form-control">
                <option value="">Select Year</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" @if(isset($selectedYear) && $selectedYear==$year) selected @endif>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Teacher</label>
            <select name="teacher_id" class="form-control">
                <option value="">All Teachers</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @if(isset($selectedTeacher) && $selectedTeacher==$teacher->id) selected @endif>{{ $teacher->user->name ?? 'N/A' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    @if($students->isNotEmpty() && $courses->isNotEmpty() && $selectedExam && $selectedYear)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="resultsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            @foreach($courses as $course)
                                <th>{{ $course->course_name }}</th>
                            @endforeach
                            <th>Average</th>
                            <th>Overall Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->name }}</td>
                                @php $sum = 0; $count = 0; @endphp
                                @foreach($courses as $course)
                                    @php
                                        $entry = $grades[$student->id][$course->id] ?? null;
                                        $mark = $entry['marks'] ?? null;
                                        $gradeText = $entry['grade'] ?? null;
                                        if(!is_null($mark)) { $sum += $mark; $count++; }
                                    @endphp
                                    <td>
                                        @if(!is_null($mark))
                                            {{ $mark }}
                                            @if($gradeText)
                                                <span class="badge bg-info ms-1">{{ $gradeText }}</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                                @php $avg = $count ? number_format($sum / $count, 2) : null; @endphp
                                <td>{{ $avg ?? '-' }}</td>
                                <td>{{ $overall[$student->id]['overall_grade'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Means</th>
                            @foreach($courses as $course)
                                @php $cMean = $courseMeans[$course->id]['average'] ?? null; $cGrade = $courseMeans[$course->id]['grade'] ?? null; @endphp
                                <th>
                                    {{ $cMean !== null ? number_format($cMean, 2) : '-' }}
                                    @if($cGrade)
                                        <span class="badge bg-secondary ms-1">{{ $cGrade }}</span>
                                    @endif
                                </th>
                            @endforeach
                            <th>
                                {{ $overallMean !== null ? number_format($overallMean, 2) : '-' }}
                            </th>
                            <th>
                                {{ $overallMeanGrade ?? '-' }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script>
$(function() {
    if ($('#resultsTable').length) {
        $('#resultsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', title: 'Results', footer: true },
                { extend: 'csvHtml5', title: 'Results', footer: true },
                { extend: 'excelHtml5', title: 'Results', footer: true },
                { extend: 'pdfHtml5', title: 'Results', footer: true },
                { extend: 'print', title: 'Results', footer: true }
            ],
            pageLength: 50
        });
    }
});
</script>
@endpush
