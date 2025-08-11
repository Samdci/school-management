@extends('layouts.backend')
@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Admin Management</h3>
            <!-- Add Admin Button -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="fas fa-user-plus me-1"></i>
                Add Admin
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Phonenumber</th>
                            <th>Gender</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr>
                            <td>{{ $admin->user->name }}</td>
                            <td>{{ $admin->user->username }}</td>
                            <td>{{ $admin->user->email }}</td>
                            <td>{{ $admin->phonenumber }}</td>
                            <td>{{ ucfirst($admin->gender) }}</td>
                            <td>{{ $admin->user->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle button button2" type="button" id="dropdownMenuButton{{ $admin->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Fullname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phonenumber</th>
                            <th>Gender</th>
                            <th>Account Status</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admins.store') }}">
                @csrf
                <input type="hidden" name="role" value="admin">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdminModalLabel">Add Admin</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_name">Full Name</label>
                        <input type="text" class="form-control" id="add_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="add_username">Username</label>
                        <input type="text" class="form-control" id="add_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="add_email">Email</label>
                        <input type="email" class="form-control" id="add_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="add_phonenumber">Phonenumber</label>
                        <input type="text" class="form-control" id="add_phonenumber" name="phonenumber">
                    </div>
                    <div class="form-group">
                        <label for="add_gender">Gender</label>
                        <select class="form-control" id="add_gender" name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_is_active">Account Status</label>
                        <select class="form-control" id="add_is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_must_change_password">Must Change Password</label>
                        <select class="form-control" id="add_must_change_password" name="must_change_password" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_password">Password</label>
                        <input type="password" class="form-control" id="add_password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="add_password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="add_password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger float-start" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Add Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="editAdminForm">
                @csrf
                @method('PUT')
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <button type="button" class="btn-close me-auto edit-modal-close-btn" data-bs-dismiss="modal" aria-label="Close" style="order: -1;"></button>
                    <h5 class="modal-title ms-2" id="editAdminModalLabel">Edit Admin</h5>
                </div>
                <div class="modal-body">
                    <div id="editAdminNameDisplay" class="mb-3 font-weight-bold text-primary"></div>
                    <div class="form-group">
                        <label for="edit_name">Full Name</label>
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
                        <input type="text" class="form-control" id="edit_phonenumber" name="phonenumber">
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
                        <label for="edit_is_active">Account Status</label>
                        <select class="form-control" id="edit_is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_must_change_password">Must Change Password</label>
                        <select class="form-control" id="edit_must_change_password" name="must_change_password" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Admin Modal -->
<div class="modal fade" id="viewAdminModal" tabindex="-1" role="dialog" aria-labelledby="viewAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAdminModalLabel">View Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewAdminNameDisplay" class="mb-3 font-weight-bold text-primary"></div>
                <div class="form-group">
                    <label for="view_username">Username</label>
                    <input type="text" class="form-control" id="view_username" disabled>
                </div>
                <div class="form-group">
                    <label for="view_email">Email</label>
                    <input type="email" class="form-control" id="view_email" disabled>
                </div>
                <div class="form-group">
                    <label for="view_phonenumber">Phonenumber</label>
                    <input type="text" class="form-control" id="view_phonenumber" disabled>
                </div>
                <div class="form-group">
                    <label for="view_gender">Gender</label>
                    <input type="text" class="form-control" id="view_gender" disabled>
                </div>
                <div class="form-group">
                    <label for="view_is_active">Account Status</label>
                    <input type="text" class="form-control" id="view_is_active" disabled>
                </div>
                <div class="form-group">
                    <label for="view_must_change_password">Must Change Password</label>
                    <input type="text" class="form-control" id="view_must_change_password" disabled>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteAdminModal" tabindex="-1" role="dialog" aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="deleteAdminForm">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAdminModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this admin?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fill Edit Modal with admin data
    $(document).on('click', '.editAdminBtn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var username = $(this).data('admin-id');
        var email = $(this).data('email');
        var phonenumber = $(this).data('phonenumber');
        var gender = $(this).data('gender');
        var is_active = $(this).data('is_active');
        var must_change_password = $(this).data('must_change_password');
        $('#editAdminForm').attr('action', '/admins/' + id);
        $('#edit_name').val(name);
        $('#edit_username').val(username);
        $('#edit_email').val(email);
        $('#edit_phonenumber').val(phonenumber);
        $('#edit_gender').val(gender);
        $('#edit_is_active').val(is_active);
        $('#edit_must_change_password').val(must_change_password);
        $('#editAdminNameDisplay').text('Editing: ' + name);
    });
    // Fill View Modal with admin data
    $(document).on('click', '.viewAdminBtn', function() {
        var name = $(this).data('name');
        var username = $(this).data('admin-id');
        var email = $(this).data('email');
        var phonenumber = $(this).data('phonenumber');
        var gender = $(this).data('gender');
        var is_active = $(this).data('is_active') == 1 ? 'Active' : 'Inactive';
        var must_change_password = $(this).data('must_change_password') == 1 ? 'Yes' : 'No';
        $('#viewAdminNameDisplay').text('Admin: ' + name);
        $('#view_username').val(username);
        $('#view_email').val(email);
        $('#view_phonenumber').val(phonenumber);
        $('#view_gender').val(gender);
        $('#view_is_active').val(is_active);
        $('#view_must_change_password').val(must_change_password);
    });
    // Open delete modal and set form action
    $(document).on('click', '.deleteAdminLink', function(e) {
        e.preventDefault();
        var adminId = $(this).data('admin-id');
        $('#deleteAdminForm').attr('action', '/admins/' + adminId);
        $('#deleteAdminModal').modal('show');
    });
</script>
@endpush

<style>
  .edit-modal-close-btn:hover {
      opacity: 0.75;
  }
</style>

@endsection
