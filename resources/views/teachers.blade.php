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
        <div class="box-header d-flex justify-content-between align-items-center">
            <h3 class="box-title">All Teachers</h3>
            <button type="button" class="btn btn-primary button pull-right" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                <i class="fa fa-user-plus"></i>
                Add Teacher
            </button>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phonenumber</th>
                            <th>Gender</th>
                            <th>Class Assigned</th>
                            <th>Home County</th>
                            <th>Action</th>
                            <!--<th>Role</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->user->name ?? '' }}</td>
                            <td>{{ $teacher->user->username ?? '' }}</td>
                            <td>{{ $teacher->user->email ?? '' }}</td>
                            <td>{{ $teacher->phonenumber }}</td>
                            <td>{{ ucfirst($teacher->gender) }}</td>
                            <td>{{ $teacher->studentClass->class_name ?? '' }}</td>
                            <td>{{ $teacher->homecounty }}</td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm  button button2" type="button" id="dropdownMenuButton{{ $teacher->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $teacher->id }}">
                                        <a class="dropdown-item editTeacherBtn" href="#"
                                            data-id="{{ $teacher->id }}"
                                            data-name="{{ $teacher->user->name ?? '' }}"
                                            data-username="{{ $teacher->user->username ?? '' }}"
                                            data-email="{{ $teacher->user->email ?? '' }}"
                                            data-phonenumber="{{ $teacher->phonenumber }}"
                                            data-gender="{{ $teacher->gender }}"
                                            data-homecounty="{{ $teacher->homecounty }}"
                                            data-student_class_id="{{ $teacher->student_class_id }}"
                                            data-bs-toggle="modal" data-bs-target="#editTeacherModal">
                                            Edit
                                        </a>
                                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <!--<td>{{ $teacher->role }}</td>-->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit teacher Modal -->
<div class="modal fade" id="editTeacherModal" tabindex="-1" role="dialog" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" id="editTeacherForm">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title" id="editTeacherModalLabel">Edit Teacher</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <div class="form-group">
                <label for="edit_student_class_id">Class Assigned</label>
                <select class="form-control" name="student_class_id" id="edit_student_class_id">
                    <option value="">Select</option>
                    @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->class_name ?? 'NA' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="edit_name">Fullname</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>
            <div class="form-group">
                <label for="edit_username">Username</label>
                <input type="text" class="form-control" id="edit_username" name="username" required>
            </div>
            <div class="form-group">
                <label for="edit_email">Email</label>
                <input type="email" class="form-control" id="edit_email" name="email" required>
            </div>
            <div class="form-group">
                <label for="edit_phonenumber">Phonenumber</label>
                <input type="text" class="form-control" id="edit_phonenumber" name="phonenumber" required>
            </div>
            <div class="form-group">
                <label for="edit_gender">Gender</label>
                <select class="form-control" id="edit_gender" name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_homecounty">Home County</label>
                <input type="text" class="form-control" id="edit_homecounty" name="homecounty">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger button pull-left" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-sm btn-success button">Update Teacher</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" role="dialog" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('teachers.store') }}">
      @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addTeacherModalLabel">Add Teacher</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="student_class_id">Class Assigned</label>
                <select class="form-control" name="student_class_id" id="student_class_id">
                    <option value="">Select</option>
                    @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->class_name ?? 'NA' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="name">Fullname</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phonenumber">Phonenumber</label>
            <input type="text" class="form-control" id="phonenumber" name="phonenumber" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="homecounty">Home County</label>
                <input type="text" class="form-control" id="homecounty" name="homecounty">
            </div>
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger button pull-left" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-sm btn-success button">Add Teacher</button>
        </div>
    </form>
   </div>
  </div>
</div>

@push('scripts')
<script>
    // Fill Edit Modal with teacher data
    $(document).on('click', '.editTeacherBtn', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var username = $(this).data('username');
    var email = $(this).data('email');
    var phonenumber = $(this).data('phonenumber');
    var gender = $(this).data('gender');
    var homecounty = $(this).data('homecounty');
    var student_class_id = $(this).data('student_class_id');
    var course_id = $(this).data('course_id');
    $('#editTeacherForm').attr('action', '/teachers/' + id);
    $('#edit_name').val(name);
    $('#edit_username').val(username);
    $('#edit_email').val(email);
    $('#edit_phonenumber').val(phonenumber);
    $('#edit_gender').val(gender);
    $('#edit_homecounty').val(homecounty);
    $('#edit_student_class_id').val(student_class_id);
});
</script>
@endpush
@endsection
