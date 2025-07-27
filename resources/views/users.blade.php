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
    <div class="pull-right">
        <button type="button" class="btn btn-primary button" data-toggle="modal" data-target="#uploadModal">
            Upload File
        </button>
    </div>
    <div class="box">
        <div class="box-header d-flex justify-content-between align-items-center">
            <h3 class="box-title">Users List</h3>
            <!-- Add User Button -->
            <button type="button" class="btn btn-primary button pull-right" data-toggle="modal" data-target="#addUserModal">
                <i class="fa fa-user-plus"></i>
                Add User
            </button>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Phonenumber</th>
                            <th>Gender</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phonenumber }}</td>
                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle button button2" type="button" id="dropdownMenuButton{{ $user->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuButton{{ $user->id }}" style="min-width: 120px;">
                                        <i class="fa fa-user fa-add"></i>
                                        <a class="dropdown-item text-primary editUserBtn" href="#"
                                            style="font-weight: 500;"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-phonenumber="{{ $user->phonenumber }}"
                                            data-gender="{{ $user->gender }}"
                                            data-role="{{ $user->role }}"
                                            data-toggle="modal" data-target="#editUserModal">
                                            Edit
                                        </a>
                                        <div class="dropdown-divider my-0"></div>
                                        <i class="fa fa-trash"></i>
                                        <a href="#" class="dropdown-item text-danger deleteUserLink" style="font-weight: 500;" data-user-id="{{ $user->id }}">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Phonenumber</th>
                            <th>Gender</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Fullname</label>
            <input type="text" class="form-control" id="name" name="name" required>
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
            <label for="role">Role</label>
            <select class="form-control" id="role" name="role" required>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" style="border-radius: 10px;" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success" style="border-radius: 10px;">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- upload students Modal -->
<!-- <div class="modal fade" id="uploadModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span style="color:#dc3545;"><center><h4>Upload Students</h4></center></span>
            </div>
            <form method="POST" action="{{ route('uploadStudentData') }}" enctype="multipart/form-data">
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

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" id="editUserForm">
        @csrf
        @method('PUT')
        <div class="modal-header d-flex justify-content-between align-items-center">
          <button type="button" class="close mr-auto edit-modal-close-btn" data-dismiss="modal" aria-label="Close" style="order: -1;">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title ml-2" id="editUserModalLabel">Edit User</h5>
        </div>
        <div class="modal-body">
          <div id="editUserNameDisplay" class="mb-3 font-weight-bold text-primary"></div>
          <div class="form-group">
            <label for="edit_name">Fullname</label>
            <input type="text" class="form-control" id="edit_name" name="name" required>
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
        </div>
        <div class="modal-footer" style="justify-content: space-between;">
          <button type="button" class="btn btn-danger pull-left button" data-dismiss="modal button">Close</button>
          <button type="submit" class="btn btn-success button">Update User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" id="deleteUserForm">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this user?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>

    // Fill Edit Modal with user data
    $(document).on('click', '.editUserBtn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var phonenumber = $(this).data('phonenumber');
        var gender = $(this).data('gender');
        var role = $(this).data('role');
        $('#editUserForm').attr('action', '/users/' + id);
        $('#edit_name').val(name);
        $('#edit_email').val(email);
        $('#edit_phonenumber').val(phonenumber);
        $('#edit_gender').val(gender);
        $('#edit_role').val(role);
        // Display the user's name above the form
        $('#editUserNameDisplay').text('Editing: ' + name);
    });
    // Open delete modal and set form action
    $(document).on('click', '.deleteUserLink', function(e) {
        e.preventDefault();
        var userId = $(this).data('user-id');
        $('#deleteUserForm').attr('action', '/users/' + userId);
        $('#deleteUserModal').modal('show');
    });
</script>
@endpush

<style>
.edit-modal-close-btn:hover {
    color: #fff;
    background-color: #dc3545;
    border-radius: 10px;
}
.button{
    border-radius:20px;
}
</style>
@endsection
