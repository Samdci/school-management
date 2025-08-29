@extends('layouts.backend')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Audit Logs</h3>
                    <div>
                        <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                            <i class="fas fa-trash"></i> Clear Old Logs
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="event">Event Type</label>
                                    <select name="event" id="event" class="form-control form-control-sm">
                                        <option value="">All Events</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                                {{ ucfirst($event) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="user_id">User</label>
                                    <select name="user_id" id="user_id" class="form-control form-control-sm">
                                        <option value="">All Users</option>
                                        @foreach($users as $id => $name)
                                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="auditable_type">Model</label>
                                    <select name="auditable_type" id="auditable_type" class="form-control form-control-sm">
                                        <option value="">All Models</option>
                                        @foreach($auditableTypes as $type => $name)
                                            <option value="{{ $type }}" {{ request('auditable_type') == $type ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_from">From Date</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                                           value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_to">To Date</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm"
                                           value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Event</th>
                                    <th>User</th>
                                    <th>Model</th>
                                    <th>Model ID</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            <span class="badge bg-{{ $log->event === 'deleted' ? 'danger' : ($log->event === 'created' ? 'success' : 'primary') }}">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->user)
                                                {{ $log->user->name }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->auditable_type)
                                                {{ class_basename($log->auditable_type) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->auditable_id ?? 'N/A' }}</td>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @can('delete audit logs')
                                                <form action="{{ route('admin.audit-logs.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this log?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No audit logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    {{ $logs->links() }}
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" role="dialog" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearLogsModalLabel">Clear Old Logs</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.audit-logs.clear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>This will delete all audit logs older than 30 days. This action cannot be undone.</p>
                    <div class="form-group">
                        <label for="days">Delete logs older than (days):</label>
                        <input type="number" class="form-control" id="days" name="days" value="30" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Clear Old Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize select2 for better dropdowns
        $('#event, #user_id, #auditable_type').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
@endpush
