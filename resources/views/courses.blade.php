@extends('layouts.backend')

@section('content')
<div class="container">
    <h2>All Courses</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" id="success-alert" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('success') }}
        </div>
    @endif

    {{-- Add Course Form --}}
    <div class="card mb-4">
        <div class="card-header">Add New Course</div>
        <div class="card-body">
            <form action="{{ route('courses.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="course_name" class="form-control" placeholder="Course Name" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="text" name="course_code" class="form-control" placeholder="Course Code">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="category" class="form-control">
                            <option value="">Select Category</option>
                            <option value="Core">Core</option>
                            <option value="Optional">Optional</option>
                            <option value="Pre-Technical">Pre-Technical</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" name="description" class="form-control" placeholder="Description">
                    </div>
                </div>
                <div style="margin-top: 4px; margin-bottom: 10px;">
                    <button type="submit" class="btn btn-primary mt-2" style="border-radius: 20px; margin-top: 2px; right: 50px;">Add Course</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Courses List</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Course Name</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $course->course_name }}</td>
                            <td>{{ $course->course_code }}</td>
                            <td>{{ $course->category }}</td>
                            <td>{{ $course->description }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle button button2" type="button" id="dropdownMenuButton{{ $course->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu py-0" aria-labelledby="dropdownMenuButton{{ $course->id }}" style="min-width: 120px;">
                                        <li>
                                            <a href="#" class="dropdown-item text-primary" data-toggle="modal" data-target="#editModal{{ $course->id }}" style="font-weight: 500;">Edit</a>
                                        </li>
                                        <li class="dropdown-divider my-0"></li>
                                        <li>
                                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display:inline-block; margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" style="font-weight: 500; background: none; border: none; color: #d9534f; cursor: pointer;">Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $course->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $course->id }}">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('courses.update', $course->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="editModalLabel{{ $course->id }}">Edit Course</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Course Name</label>
                                                <input type="text" name="course_name" class="form-control" value="{{ $course->course_name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Course Code</label>
                                                <input type="text" name="course_code" class="form-control" value="{{ $course->course_code }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="category" class="form-control">
                                                    <option value="">Select Category</option>
                                                    <option value="Core" {{ $course->category == 'Core' ? 'selected' : '' }}>Core</option>
                                                    <option value="Optional" {{ $course->category == 'Optional' ? 'selected' : '' }}>Optional</option>
                                                    <option value="Pre-Technical" {{ $course->category == 'Pre-Technical' ? 'selected' : '' }}>Pre-Technical</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" name="description" class="form-control" value="{{ $course->description }}">
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
                        <!-- End Edit Modal -->
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

