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
            <h3 class="box-title">Classes List</h3>
            <button type="button" class="btn btn-primary button" data-toggle="modal" data-target="#addClassModal">
                Add Class
            </button>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $class)
                        <tr>
                            <td>{{ $class->class_name }}</td>
                            <td>{{ $class->category }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm button2 button" type="button" id="dropdownMenuButton{{ $class->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $class->id }}">
                                        <a class="dropdown-item editClassBtn" href="#"
                                            data-id="{{ $class->id }}"
                                            data-class_name="{{ $class->class_name }}"
                                            data-category="{{ $class->category }}"
                                            data-toggle="modal" data-target="#editClassModal">
                                            Edit
                                        </a>
                                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this class?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="addClassModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('classes.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addClassModalLabel">Add Class</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="class_name">Class Name</label>
            <input type="text" class="form-control" id="class_name" name="class_name" required>
          </div>
          <div class="form-group">
            <label for="category">Category</label>
            <select class="form-control" id="category" name="category" required>
              <option value="grade7">Grade 7</option>
              <option value="grade8">Grade 8</option>
              <option value="grade9">Grade 9</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger button pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-sm btn-success button">Add Class</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1" role="dialog" aria-labelledby="editClassModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" id="editClassForm">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editClassModalLabel">Edit Class</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="edit_class_name">Class Name</label>
            <input type="text" class="form-control" id="edit_class_name" name="class_name" required>
          </div>
          <div class="form-group">
            <label for="edit_category">Category</label>
            <select class="form-control" id="edit_category" name="category" required>
              <option value="grade7">Grade 7</option>
              <option value="grade8">Grade 8</option>
              <option value="grade9">Grade 9</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger button pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btm-sm btn-success button">Update Class</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
    // Fill Edit Modal with class data
    $(document).on('click', '.editClassBtn', function() {
        var id = $(this).data('id');
        var class_name = $(this).data('class_name');
        var category = $(this).data('category');
        $('#editClassForm').attr('action', '/classes/' + id);
        $('#edit_class_name').val(class_name);
        $('#edit_category').val(category);
    });
</script>
@endpush
@endsection
