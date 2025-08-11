@extends('layouts.backend')
@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Student Management</h3>
            <div>
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-1"></i> Upload Students
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="fas fa-user-plus me-1"></i> Add Student
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="studentsTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Class</th>
                            <th>Guardian</th>
                            <th>Guardian Email</th>
                            <th>Guardian Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email ?? 'N/A' }}</td>
                            <td>{{ ucfirst($student->gender) }}</td>
                            <td>{{ $student->studentClass->class_name ?? 'N/A' }}</td>
                            <td>{{ $student->guardian_fullname ?? 'N/A' }}</td>
                            <td>{{ $student->guardian_email ?? 'N/A' }}</td>
                            <td>{{ $student->guardian_phonenumber ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary viewStudentBtn"
                                        data-id="{{ $student->id }}"
                                        data-name="{{ $student->name }}"
                                        data-email="{{ $student->email }}"
                                        data-gender="{{ $student->gender }}"
                                        data-student-class="{{ $student->studentClass->class_name ?? 'N/A' }}"
                                        data-guardian-fullname="{{ $student->guardian_fullname }}"
                                        data-guardian-relationship="{{ $student->guardian_relationship }}"
                                        data-guardian-email="{{ $student->guardian_email }}"
                                        data-guardian-phonenumber="{{ $student->guardian_phonenumber }}"
                                        data-home-county="{{ $student->home_county }}"
                                        data-kcpe-marks="{{ $student->kcpe_marks }}"
                                        data-cert-number="{{ $student->cert_number }}"
                                        data-bs-toggle="modal" data-bs-target="#viewStudentModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning editStudentBtn"
                                        data-id="{{ $student->id }}"
                                        data-name="{{ $student->name }}"
                                        data-email="{{ $student->email }}"
                                        data-gender="{{ $student->gender }}"
                                        data-student-class-id="{{ $student->student_class_id }}"
                                        data-guardian-fullname="{{ $student->guardian_fullname }}"
                                        data-guardian-relationship="{{ $student->guardian_relationship }}"
                                        data-guardian-email="{{ $student->guardian_email }}"
                                        data-guardian-phonenumber="{{ $student->guardian_phonenumber }}"
                                        data-home-county="{{ $student->home_county }}"
                                        data-kcpe-marks="{{ $student->kcpe_marks }}"
                                        data-cert-number="{{ $student->cert_number }}"
                                        data-bs-toggle="modal" data-bs-target="#editStudentModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this student?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editStudentForm">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Student Information</h5>
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="edit_gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_student_class_id" class="form-label">Class</label>
                                <select class="form-select" id="edit_student_class_id" name="student_class_id">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Guardian Information</h5>
                            <div class="mb-3">
                                <label for="edit_guardian_fullname" class="form-label">Guardian Full Name</label>
                                <input type="text" class="form-control" id="edit_guardian_fullname" name="guardian_fullname">
                            </div>
                            <div class="mb-3">
                                <label for="edit_guardian_relationship" class="form-label">Relationship</label>
                                <input type="text" class="form-control" id="edit_guardian_relationship" name="guardian_relationship">
                            </div>
                            <div class="mb-3">
                                <label for="edit_guardian_email" class="form-label">Guardian Email</label>
                                <input type="email" class="form-control" id="edit_guardian_email" name="guardian_email">
                            </div>
                            <div class="mb-3">
                                <label for="edit_guardian_phonenumber" class="form-label">Guardian Phone</label>
                                <input type="text" class="form-control" id="edit_guardian_phonenumber" name="guardian_phonenumber">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5 class="mb-3">Additional Information</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_home_county" class="form-label">Home County</label>
                                <input type="text" class="form-control" id="edit_home_county" name="home_county">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_kcpe_marks" class="form-label">K.C.P.E Marks</label>
                                <input type="text" class="form-control" id="edit_kcpe_marks" name="kcpe_marks">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_cert_number" class="form-label">Certificate Number</label>
                                <input type="text" class="form-control" id="edit_cert_number" name="cert_number">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
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
                    <button type="button" class="btn btn-danger pull-left button" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm button">Upload students</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end of upload Modal -->

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('students.store') }}" id="addStudentForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Student Information</h5>
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="student_class_id" class="form-label">Class</label>
                                <select class="form-select" id="student_class_id" name="student_class_id">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Guardian Information</h5>
                            <div class="mb-3">
                                <label for="guardian_fullname" class="form-label">Guardian Full Name</label>
                                <input type="text" class="form-control" id="guardian_fullname" name="guardian_fullname">
                            </div>
                            <div class="mb-3">
                                <label for="guardian_relationship" class="form-label">Relationship</label>
                                <input type="text" class="form-control" id="guardian_relationship" name="guardian_relationship">
                            </div>
                            <div class="mb-3">
                                <label for="guardian_email" class="form-label">Guardian Email</label>
                                <input type="email" class="form-control" id="guardian_email" name="guardian_email">
                            </div>
                            <div class="mb-3">
                                <label for="guardian_phonenumber" class="form-label">Guardian Phone</label>
                                <input type="text" class="form-control" id="guardian_phonenumber" name="guardian_phonenumber">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5 class="mb-3">Additional Information</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="home_county" class="form-label">Home County</label>
                                <input type="text" class="form-control" id="home_county" name="home_county">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kcpe_marks" class="form-label">K.C.P.E Marks</label>
                                <input type="text" class="form-control" id="kcpe_marks" name="kcpe_marks">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cert_number" class="form-label">Certificate Number</label>
                                <input type="text" class="form-control" id="cert_number" name="cert_number">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with responsive and export options
    $('#studentsTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
        "responsive": true,
        "dom": 'Bfrtip',
        "buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    // Reset form when modal is closed
    $('#addStudentModal, #editStudentModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('form').removeClass('was-validated');
    });

    // Form validation for add student
    $('#addStudentForm').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });

    // Form validation for edit student
    $('#editStudentForm').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });

    // Edit Student button click handler
    $(document).on('click', '.editStudentBtn', function(e) {
        e.preventDefault();
        
        // Get student data from data attributes
        var $btn = $(this);
        var studentData = {
            id: $btn.data('id'),
            name: $btn.data('name') || '',
            email: $btn.data('email') || '',
            gender: $btn.data('gender') || '',
            student_class_id: $btn.data('student-class-id') || '',
            guardian_fullname: $btn.data('guardian-fullname') || '',
            guardian_relationship: $btn.data('guardian-relationship') || '',
            guardian_email: $btn.data('guardian-email') || '',
            guardian_phonenumber: $btn.data('guardian-phonenumber') || '',
            home_county: $btn.data('home-county') || '',
            kcpe_marks: $btn.data('kcpe-marks') || '',
            cert_number: $btn.data('cert-number') || ''
        };

        console.log('Student Data:', studentData); // Debug log

        // Set form action URL
        var updateUrl = "{{ route('students.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', studentData.id);
        
        // Populate form fields
        $('#editStudentForm').attr('action', updateUrl);
        $('#edit_id').val(studentData.id);
        $('#edit_name').val(studentData.name);
        $('#edit_email').val(studentData.email);
        
        // Set gender select
        if (studentData.gender) {
            $('#edit_gender').val(studentData.gender);
        }
        
        // Set class select
        if (studentData.student_class_id) {
            $('#edit_student_class_id').val(studentData.student_class_id);
        }
        
        // Populate guardian fields
        $('#edit_guardian_fullname').val(studentData.guardian_fullname);
        $('#edit_guardian_relationship').val(studentData.guardian_relationship);
        $('#edit_guardian_email').val(studentData.guardian_email);
        $('#edit_guardian_phonenumber').val(studentData.guardian_phonenumber);
        
        // Populate additional info
        $('#edit_home_county').val(studentData.home_county);
        $('#edit_kcpe_marks').val(studentData.kcpe_marks);
        $('#edit_cert_number').val(studentData.cert_number);
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
        editModal.show();
        
        // Reset validation
        $('#editStudentForm').removeClass('was-validated');
    });

    // Delete Student with confirmation
    $(document).on('click', '.deleteStudentBtn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var studentId = $btn.data('id');
        var studentName = $btn.data('name') || 'this student';
        
        if (confirm('Are you sure you want to delete ' + studentName + '? This action cannot be undone.')) {
            // Show loading state
            var originalText = $btn.html();
            $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...').prop('disabled', true);
            
            // Get CSRF token
            var token = $('meta[name="csrf-token"]').attr('content');
            
            // Send delete request
            $.ajax({
                url: '/students/' + studentId,
                type: 'POST',
                data: {
                    _token: token,
                    _method: 'DELETE'
                },
                success: function(response) {
                    // Show success message
                    var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                   'Student deleted successfully.' +
                                   '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                   '</div>';
                    $('.container-fluid').prepend(alertHtml);
                    
                    // Reload the page after a short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    alert('Error: Could not delete student. Please try again.');
                    $btn.html(originalText).prop('disabled', false);
                }
            });
        }
    });

    // Handle form submission - let the form submit normally to the controller
    $(document).on('submit', '#addStudentForm, #editStudentForm', function() {
        // Show loading state on the submit button
        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
    });
});
</script>
@endpush
@endsection
