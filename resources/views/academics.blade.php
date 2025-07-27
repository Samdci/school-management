@extends('layouts.backend')
@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" id="success-alert" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" id="error-alert" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('error') }}
        </div>
    @endif
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Terms</h3>
            <button type="button" class="btn btn-primary button pull-right" data-toggle="modal" data-target="#addTermModal">
                Add Term
            </button>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Term Name</th>
                        <th>Year</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($terms as $term)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $term->term_name }}</td>
                        <td>{{ $term->term_year }}</td>
                        <td>{{ $term->start_date }}</td>
                        <td>{{ $term->end_date }}</td>
                        <td>{{ $term->status }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle button button2" type="button" id="dropdownMenuButtonTerm{{ $term->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu py-0" aria-labelledby="dropdownMenuButtonTerm{{ $term->id }}" style="min-width: 120px;">
                                    <li>
                                        <a href="#" class="dropdown-item text-primary" data-toggle="modal" data-target="#editTermModal{{ $term->id }}" style="font-weight: 500;">Edit</a>
                                    </li>
                                    <li class="dropdown-divider my-0"></li>
                                    <li>
                                        <form action="{{ route('academics.destroy', $term->id) }}" method="POST" style="">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" style="font-weight: 500; background: none; border: none; color: #d9534f; cursor: pointer;">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- Edit Term Modal -->
                    <div class="modal fade" id="editTermModal{{ $term->id }}" tabindex="-1" role="dialog" aria-labelledby="editTermModalLabel{{ $term->id }}">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('academics.update', $term->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="editTermModalLabel{{ $term->id }}">Edit Term</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Term Name</label>
                                            <input type="text" name="term_name" class="form-control" value="{{ $term->term_name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Year</label>
                                            <input type="number" name="term_year" class="form-control" value="{{ $term->term_year }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" name="start_date" class="form-control" value="{{ $term->start_date }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" name="end_date" class="form-control" value="{{ $term->end_date }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <input type="text" name="status" class="form-control" value="{{ $term->status }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger pull-left btn-sm button" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-success button">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Edit Term Modal -->
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Add Term Modal -->
    <div class="modal fade" id="addTermModal" tabindex="-1" role="dialog" aria-labelledby="addTermModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('academics.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="addTermModalLabel">Add Term</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Term Name</label>
                            <select name="term_name" class="form-control" required>
                                <option value="Term 1">Term 1</option>
                                <option value="Term 2">Term 2</option>
                                <option value="Term 3">Term 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" name="term_year" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="ongoing">Ongoing</option>
                                <option value="complete">Complete</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-left btn-sm button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success button">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Add Term Modal -->

    <!-- Exams Table CRUD -->
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Exams</h3>
            <button type="button" class="btn btn-primary button pull-right" data-toggle="modal" data-target="#addExamModal">
                Add Exam
            </button>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exam Name</th>
                        <th>Term</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exams as $exam)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $exam->name }}</td>
                        <td>{{ $exam->term->term_name ?? '' }} ({{ $exam->term->term_year ?? '' }})</td>
                        <td>{{ $exam->created_at }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle button button2" type="button" id="dropdownMenuButtonExam{{ $exam->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu py-0" aria-labelledby="dropdownMenuButtonExam{{ $exam->id }}" style="min-width: 120px;">
                                    <li>
                                        <a href="#" class="dropdown-item text-primary" data-toggle="modal" data-target="#editExamModal{{ $exam->id }}" style="font-weight: 500;">Edit</a>
                                    </li>
                                    <li class="dropdown-divider my-0"></li>
                                    <li>
                                        <form action="{{ route('academics.destroy', $exam->id) }}" method="POST" style="">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" style="font-weight: 500; background: none; border: none; color: #d9534f; cursor: pointer;">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- Edit Exam Modal -->
                    <div class="modal fade" id="editExamModal{{ $exam->id }}" tabindex="-1" role="dialog" aria-labelledby="editExamModalLabel{{ $exam->id }}">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('academics.update', $exam->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="editExamModalLabel{{ $exam->id }}">Edit Exam</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Exam Name</label>
                                            <select name="name" class="form-control" required>
                                                <option value="Opener" {{ $exam->name == 'Opener' ? 'selected' : '' }}>Opener</option>
                                                <option value="Midterm" {{ $exam->name == 'Midterm' ? 'selected' : '' }}>Midterm</option>
                                                <option value="End term" {{ $exam->name == 'End term' ? 'selected' : '' }}>End term</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Term</label>
                                            <select name="term_id" class="form-control" required>
                                                @foreach($terms as $termOption)
                                                    <option value="{{ $termOption->id }}" {{ $exam->term_id == $termOption->id ? 'selected' : '' }}>{{ $termOption->term_name }} ({{ $termOption->term_year }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger pull-left btn-sm button" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-success button">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Edit Exam Modal -->
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Add Exam Modal -->
    <div class="modal fade" id="addExamModal" tabindex="-1" role="dialog" aria-labelledby="addExamModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('academics.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="addExamModalLabel">Add Exam</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Exam Name</label>
                            <select name="name" class="form-control" required>
                                <option value="Opener">Opener</option>
                                <option value="Midterm">Midterm</option>
                                <option value="End term">End term</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Term</label>
                            <select name="term_id" class="form-control" required>
                                @foreach($terms as $termOption)
                                    <option value="{{ $termOption->id }}">{{ $termOption->term_name }} ({{ $termOption->term_year }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-left btn-sm button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success button">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Add Exam Modal -->

    <!-- Exam Courses Table CRUD -->
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Exam Courses</h3>
            <button type="button" class="btn btn-primary button pull-right" data-toggle="modal" data-target="#addExamCourseModal">
                Add Exam Course
            </button>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exam</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($examCourses as $examCourse)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $examCourse->exam->name ?? '' }}</td>
                        <td>{{ $examCourse->course->course_name ?? '' }}</td>
                        <td>{{ $examCourse->exam->term->term_year ?? '' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle button button2" type="button" id="dropdownMenuButtonExamCourse{{ $examCourse->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu py-0" aria-labelledby="dropdownMenuButtonExamCourse{{ $examCourse->id }}" style="min-width: 120px;">
                                    <li>
                                        <a href="#" class="dropdown-item text-primary" data-toggle="modal" data-target="#editExamCourseModal{{ $examCourse->id }}" style="font-weight: 500;">Edit</a>
                                    </li>
                                    <li class="dropdown-divider my-0"></li>
                                    <li>
                                        <form action="{{ route('academics.destroy', $examCourse->id) }}" method="POST" style="">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" style="font-weight: 500; background: none; border: none; color: #d9534f; cursor: pointer;">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- Edit Exam Course Modal -->
                    <div class="modal fade" id="editExamCourseModal{{ $examCourse->id }}" tabindex="-1" role="dialog" aria-labelledby="editExamCourseModalLabel{{ $examCourse->id }}">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('academics.update', $examCourse->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="editExamCourseModalLabel{{ $examCourse->id }}">Edit Exam Course</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Exam</label>
                                            <select name="exam_id" class="form-control" required>
                                                @foreach($exams as $examOption)
                                                    <option value="{{ $examOption->id }}" {{ $examCourse->exam_id == $examOption->id ? 'selected' : '' }}>{{ $examOption->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Course</label>
                                            <select name="course_id" class="form-control" required>
                                                @foreach(App\Models\Course::all() as $courseOption)
                                                    <option value="{{ $courseOption->id }}" {{ $examCourse->course_id == $courseOption->id ? 'selected' : '' }}>{{ $courseOption->course_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger pull-left btn-sm button" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-success button">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Edit Exam Course Modal -->
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Add Exam Course Modal -->
    <div class="modal fade" id="addExamCourseModal" tabindex="-1" role="dialog" aria-labelledby="addExamCourseModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('academics.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="addExamCourseModalLabel">Add Exam Course</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Exam</label>
                            <select name="exam_id" class="form-control" required>
                                @foreach($exams as $examOption)
                                    <option value="{{ $examOption->id }}">{{ $examOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <select name="course_id" class="form-control" required>
                                @foreach(App\Models\Course::all() as $courseOption)
                                    <option value="{{ $courseOption->id }}">{{ $courseOption->course_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-left btn-sm button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success button">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Add Exam Course Modal -->
</div>
@endsection
