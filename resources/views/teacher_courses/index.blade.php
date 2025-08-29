@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2>Teacher-Course Assignments</h2>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                <i class="bi bi-plus"></i> Add Assignment
            </button>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-4">
            <select name="teacher_id" class="form-select">
                <option value="">All Teachers</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @if(isset($filter_teacher_id) && $filter_teacher_id==$teacher->id) selected @endif>{{ $teacher->user->name ?? 'N/A' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select name="course_id" class="form-select">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @if(isset($filter_course_id) && $filter_course_id==$course->id) selected @endif>{{ $course->course_name }} ({{ $course->course_code }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="is_primary" class="form-select">
                <option value="">All Roles</option>
                <option value="1" @if(isset($filter_is_primary) && $filter_is_primary==='1') selected @endif>Head of Department</option>
                <option value="0" @if(isset($filter_is_primary) && $filter_is_primary==='0') selected @endif>Regular</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filter</button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Teacher</th>
                        <th>Course</th>
                        <th>Head of Dept?</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($assignments as $assignment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $assignment->teacher->user->name ?? 'N/A' }}</td>
                        <td>{{ $assignment->course->course_name ?? 'N/A' }} ({{ $assignment->course->course_code ?? '' }})</td>
                        <td>{!! $assignment->is_primary ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editAssignmentModal{{ $assignment->id }}">Edit</button>
                            <form action="{{ route('teacher_courses.destroy', $assignment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this assignment?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editAssignmentModal{{ $assignment->id }}" tabindex="-1" aria-labelledby="editAssignmentModalLabel{{ $assignment->id }}" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form action="{{ route('teacher_courses.update', $assignment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                              <h5 class="modal-title" id="editAssignmentModalLabel{{ $assignment->id }}">Edit Assignment</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Teacher</label>
                                    <select name="teacher_id" class="form-select" required>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" @if($assignment->teacher_id == $teacher->id) selected @endif>{{ $teacher->user->name ?? 'N/A' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Course</label>
                                    <select name="course_id" class="form-select" required>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" @if($assignment->course_id == $course->id) selected @endif>{{ $course->course_name }} ({{ $course->course_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label><input type="checkbox" name="is_primary" value="1" @if($assignment->is_primary) checked @endif> Head of Department</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                @empty
                    <tr><td colspan="5" class="text-center">No assignments found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1" aria-labelledby="addAssignmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('teacher_courses.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addAssignmentModalLabel">Add Teacher-Course Assignment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label>Teacher</label>
                <select name="teacher_id" class="form-select" required>
                    <option value="">Select Teacher</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_name }} ({{ $course->course_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label><input type="checkbox" name="is_primary" value="1"> Head of Department</label>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Assignment</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
