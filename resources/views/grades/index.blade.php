@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Grade Entry</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Step 1: Filter Form -->
            @if($step === 'filter')
                <form method="GET" action="{{ route('grades.index') }}" class="mb-4">
                    <input type="hidden" name="step" value="filter">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="class_id" class="form-control" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                                            {{ $class->class_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Exam</label>
                                <select name="exam_id" class="form-control" required>
                                    <option value="">Select Exam</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ $selectedExam == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->name }} ({{ $exam->term->term_name }} {{ $exam->term->term_year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Year</label>
                                <select name="year" class="form-control" required>
                                    <option value="">Select Year</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Next</button>
                        </div>
                    </div>
                </form>
            @endif

            <!-- Step 2: Course Selection (Teachers Only) -->
            @if($step === 'select-course' && $isTeacher)
                <form method="GET" action="{{ route('grades.index') }}" class="mb-4">
                    <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                    <input type="hidden" name="exam_id" value="{{ $selectedExam }}">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <input type="hidden" name="step" value="select-course">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Course</label>
                                <select name="course_id" class="form-control" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ $selectedCourse == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }} ({{ $course->course_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Enter Marks</button>
                        </div>
                    </div>
                </form>
            @endif

            <!-- Step 3: Grade Entry -->
            @if($step === 'enter-marks')
                <form method="POST" action="{{ route('grades.store') }}">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $selectedExam }}">
                    <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                    @if($isTeacher)
                        <input type="hidden" name="course_id" value="{{ $selectedCourse }}">
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    @if(!$isTeacher)
                                        <th>Course</th>
                                    @endif
                                    <th>Marks</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    @if($isTeacher)
                                        <!-- Teacher view: single course -->
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>
                                                <input type="number" 
                                                       name="marks[{{ $student->id }}][{{ $selectedCourse }}]" 
                                                       class="form-control" 
                                                       value="{{ $student->grade->marks ?? '' }}"
                                                       min="0" max="100">
                                            </td>
                                            <td>{{ $student->grade->grade ?? '' }}</td>
                                        </tr>
                                    @else
                                        <!-- Admin view: all courses -->
                                        @foreach($courses as $course)
                                            <tr>
                                                @if($loop->first)
                                                    <td rowspan="{{ $courses->count() }}">{{ $student->name }}</td>
                                                @endif
                                                <td>{{ $course->course_name }}</td>
                                                <td>
                                                    <input type="number" 
                                                           name="marks[{{ $student->id }}][{{ $course->id }}]" 
                                                           class="form-control" 
                                                           value="{{ $student->grades[$course->id]->marks ?? '' }}"
                                                           min="0" max="100">
                                                </td>
                                                <td>{{ $student->grades[$course->id]->grade ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">Save Grades</button>
                        <a href="{{ route('grades.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            @endif

            @if($step === 'enter-marks')
                <!-- Edit Grade Modal -->
                <div class="modal fade" id="editGradeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form id="editGradeForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Grade</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="grade_id" id="edit_grade_id">
                                    <div class="form-group">
                                        <label>Student</label>
                                        <input type="text" class="form-control" id="edit_student_name" readonly>
                                    </div>
                                    @if(!$isTeacher)
                                    <div class="form-group">
                                        <label>Course</label>
                                        <input type="text" class="form-control" id="edit_course_name" readonly>
                                    </div>
                                    @endif
                                    <div class="form-group">
                                        <label>Marks</label>
                                        <input type="number" name="marks" id="edit_marks" class="form-control" min="0" max="100" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Grade</label>
                                        <input type="text" id="edit_grade" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            
                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteGradeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form id="deleteGradeForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this grade?</p>
                                    <input type="hidden" name="grade_id" id="delete_grade_id">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            
            @push('scripts')
            <script>
            $(document).ready(function() {
                // Handle edit button click
                $('.edit-grade').on('click', function() {
                    const gradeId = $(this).data('id');
                    const studentName = $(this).data('student');
                    const courseName = $(this).data('course');
                    const marks = $(this).data('marks');
                    const grade = $(this).data('grade');
            
                    $('#edit_grade_id').val(gradeId);
                    $('#edit_student_name').val(studentName);
                    $('#edit_course_name').val(courseName || '');
                    $('#edit_marks').val(marks);
                    $('#edit_grade').val(grade);
            
                    // Update form action
                    $('#editGradeForm').attr('action', `/grades/${gradeId}`);
            
                    // Show modal
                    $('#editGradeModal').modal('show');
                });
            
                // Handle delete button click
                $('.delete-grade').on('click', function(e) {
                    e.preventDefault();
                    const gradeId = $(this).data('id');
                    $('#delete_grade_id').val(gradeId);
                    $('#deleteGradeForm').attr('action', `/grades/${gradeId}`);
                    $('#deleteGradeModal').modal('show');
                });
            
                // Auto-calculate grade when marks change
                $('#edit_marks').on('change', function() {
                    const marks = $(this).val();
                    // This should match your calculateGrade function in the controller
                    let grade = '';
                    if (marks >= 75) grade = 'Exceeding Expectation';
                    else if (marks >= 65) grade = 'Meeting Expectation';
                    else if (marks >= 50) grade = 'Approaching Expectation';
                    else grade = 'Below Expectation';
                    $('#edit_grade').val(grade);
                });
            });
            </script>
            @endpush
        </div>
    </div>
</div>
@endsection