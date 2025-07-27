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
            <h3 class="box-title">All Students</h3>
            <button type="button" class="btn btn-primary button pull-right" data-toggle="modal" data-target="#addStudentModal">
                <i class="fa fa-user-plus"></i>
                Add Student
            </button>

            <div class="pull-right" style="margin-right: 10px;">
                <button type="button" class="btn btn-primary button" data-toggle="modal" data-target="#uploadModal">
                    Upload File
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Guardian Email</th>
                            <th>Phonenumber</th>
                            <th>Gender</th>
                            <th>Student Class</th>
                            <th>Guardian Name</th>
                            <th>Guardian Relationship</th>
                            <th>Guardian Phone</th>
                            <th>Home County</th>
                            <th>K.C.P.E Marks</th>
                            <th>Cert Number</th>
                            <th>Action</th>
                            <!--<th>Role</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->phonenumber }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>{{ $student->class_name }}</td>
                            <td>{{ $student->guardian_fullname }}</td>
                            <td>{{ $student->guardian_relationship }}</td>
                            <td>{{ $student->guardian_phonenumber }}</td>
                            <td>{{ $student->home_county }}</td>
                            <td>{{ $student->kcpe_marks }}</td>
                            <td>{{ $student->cert_copy }}</td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm  button button2" type="button" id="dropdownMenuButton{{ $student->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $student->id }}">
                                        <a class="dropdown-item editStudentBtn" href="#"
                                            data-id="{{ $student->id }}"
                                            data-name="{{ $student->name }}"
                                            data-email="{{ $student->email }}"
                                            data-phonenumber="{{ $student->phonenumber }}"
                                            data-gender="{{ $student->gender }}"
                                            data-student_class_id="{{ $student->student_class_id }}"
                                            data-guardian_fullname="{{ $student->guardian_fullname }}"
                                            data-guardian_relationship="{{ $student->guardian_relationship }}"
                                            data-guardian_phonenumber="{{ $student->guardian_phonenumber }}"
                                            data-home_county="{{ $student->home_county }}"
                                            data-kcpe_marks="{{ $student->kcpe_marks }}"
                                            data-cert_copy="{{ $student->cert_copy }}"
                                            data-toggle="modal" data-target="#editStudentModal">
                                            Edit
                                        </a>
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this student?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <!--<td>{{ $student->role }}</td>-->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" id="editStudentForm">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="name">Class</label>
                <select class="form-control" id="edit_student_class_id" name="student_class_id">
                    <option value="">Select</option>
                    @foreach ($classes as $class)
                    <option value="{{ $class->id }}" >{{ $class->class_name ?? 'NA' }}</option>
                    @endforeach

                </select>
              </div>
          <div class="form-group">
            <label for="edit_name">Fullname</label>
            <input type="text" class="form-control" id="edit_name" name="name" required>
          </div>
          <div class="form-group">
            <label for="edit_email">Guardian Email</label>
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
            <label for="edit_guardian_fullname">Guardian Fullname</label>
            <input type="text" class="form-control" id="edit_guardian_fullname" name="guardian_fullname">
          </div>
          <div class="form-group">
            <label for="edit_guardian_relationship">Guardian Relationship</label>
            <input type="text" class="form-control" id="edit_guardian_relationship" name="guardian_relationship">
          </div>
          <div class="form-group">
            <label for="edit_guardian_phonenumber">Guardian Phone</label>
            <input type="text" class="form-control" id="edit_guardian_phonenumber" name="guardian_phonenumber">
          </div>
          <div class="form-group">
            <label for="edit_home_county">Home County</label>
            <input type="text" class="form-control" id="edit_home_county" name="home_county">
          </div>
          <div class="form-group">
            <label for="edit_kcpe_marks">K.C.P.E Marks</label>
            <input type="text" class="form-control" id="edit_kcpe_marks" name="kcpe_marks">
          </div>
          <div class="form-group">
            <label for="edit_cert_copy">Cert Number</label>
            <input type="text" class="form-control" id="edit_cert_copy" name="cert_copy">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger button pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-sm btn-success button">Update Student</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- upload students Modal -->
<div class="modal fade" id="uploadModal">
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

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('students.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="name">Class</label>
                <select class="form-control" name="student_class_id">
                    <option value="">Select</option>
                    @foreach ($classes as $class)
                    <option value="{{ $class->id }}" >{{ $class->class_name ?? 'NA' }}</option>
                    @endforeach

                </select>
              </div>

          <div class="form-group">
            <label for="name">Fullname</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="email">Guardian Email</label>
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
            <label for="guardian_fullname">Guardian Fullname</label>
            <input type="text" class="form-control" id="guardian_fullname" name="guardian_fullname">
          </div>
          <div class="form-group">
            <label for="guardian_relationship">Guardian Relationship</label>
            <input type="text" class="form-control" id="guardian_relationship" name="guardian_relationship">
          </div>
          <div class="form-group">
            <label for="guardian_phonenumber">Guardian Phone</label>
            <input type="text" class="form-control" id="guardian_phonenumber" name="guardian_phonenumber">
          </div>
          <div class="form-group">
            <label for="home_county">Home County</label>
            <input type="text" class="form-control" id="home_county" name="home_county">
          </div>
          <div class="form-group">
            <label for="kcpe_marks">K.C.P.E Marks</label>
            <input type="text" class="form-control" id="kcpe_marks" name="kcpe_marks">
          </div>
          <div class="form-group">
            <label for="cert_copy">Cert Number</label>
            <input type="text" class="form-control" id="cert_copy" name="cert_copy">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger button pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-sm btn-success button">Add Student</button>
        </div>
      </form>
    </div>
  </div>
</div>
@push('scripts')
<script>
    // Fill Edit Modal with student data
    $(document).on('click', '.editStudentBtn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var phonenumber = $(this).data('phonenumber');
        var gender = $(this).data('gender');
        var student_class_id = $(this).data('student_class_id');
        var guardian_fullname = $(this).data('guardian_fullname');
        var guardian_relationship = $(this).data('guardian_relationship');
        var guardian_phonenumber = $(this).data('guardian_phonenumber');
        var home_county = $(this).data('home_county');
        var kcpe_marks = $(this).data('kcpe_marks');
        var cert_copy = $(this).data('cert_copy');
        $('#editStudentForm').attr('action', '/students/' + id);
        $('#edit_name').val(name);
        $('#edit_email').val(email);
        $('#edit_phonenumber').val(phonenumber);
        $('#edit_gender').val(gender);
        $('#edit_student_class_id').val(student_class_id);
        $('#edit_guardian_fullname').val(guardian_fullname);
        $('#edit_guardian_relationship').val(guardian_relationship);
        $('#edit_guardian_phonenumber').val(guardian_phonenumber);
        $('#edit_home_county').val(home_county);
        $('#edit_kcpe_marks').val(kcpe_marks);
        $('#edit_cert_copy').val(cert_copy);
    });
</script>
@endpush
@endsection
