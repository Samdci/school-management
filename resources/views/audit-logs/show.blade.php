@extends('layouts.backend')

@section('title', 'Audit Log Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Audit Log Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Logs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID:</th>
                                    <td>{{ $log->id }}</td>
                                </tr>
                                <tr>
                                    <th>Event:</th>
                                    <td>
                                        <span class="badge bg-{{ $log->event === 'deleted' ? 'danger' : ($log->event === 'created' ? 'success' : 'primary') }}">
                                            {{ ucfirst($log->event) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>User:</th>
                                    <td>
                                        @if($log->user)
                                            {{ $log->user->name }} ({{ $log->user->email }})
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Model:</th>
                                    <td>
                                        @if($log->auditable_type)
                                            {{ class_basename($log->auditable_type) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Model ID:</th>
                                    <td>{{ $log->auditable_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>IP Address:</th>
                                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>User Agent:</th>
                                    <td>{{ $log->user_agent ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>URL:</th>
                                    <td><a href="{{ $log->url }}" target="_blank">{{ Str::limit($log->url, 50) }}</a></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Changes</h3>
                                </div>
                                <div class="card-body p-0">
                                    @if($log->event === 'updated' || $log->event === 'deleted')
                                        @if(!empty($log->old_values) && is_array($log->old_values))
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Field</th>
                                                        <th>Old Value</th>
                                                        @if($log->event === 'updated')
                                                            <th>New Value</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($log->old_values as $field => $oldValue)
                                                        @if($log->event === 'deleted' || !isset($log->new_values[$field]) || $oldValue != $log->new_values[$field])
                                                            <tr>
                                                                <td><code>{{ $field }}</code></td>
                                                                <td>
                                                                    @if(is_array($oldValue) || is_object($oldValue))
                                                                        <pre class="mb-0">{{ json_encode($oldValue, JSON_PRETTY_PRINT) }}</pre>
                                                                    @elseif(is_bool($oldValue))
                                                                        {{ $oldValue ? 'true' : 'false' }}
                                                                    @elseif(is_null($oldValue))
                                                                        <span class="text-muted">null</span>
                                                                    @else
                                                                        {{ $oldValue }}
                                                                    @endif
                                                                </td>
                                                                @if($log->event === 'updated')
                                                                    <td>
                                                                        @php $newValue = $log->new_values[$field] ?? null; @endphp
                                                                        @if(is_array($newValue) || is_object($newValue))
                                                                            <pre class="mb-0">{{ json_encode($newValue, JSON_PRETTY_PRINT) }}</pre>
                                                                        @elseif(is_bool($newValue))
                                                                            {{ $newValue ? 'true' : 'false' }}
                                                                        @elseif(is_null($newValue))
                                                                            <span class="text-muted">null</span>
                                                                        @else
                                                                            {{ $newValue }}
                                                                        @endif
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="p-3 text-muted">No change details available.</div>
                                        @endif
                                    @elseif($log->event === 'created' && !empty($log->new_values))
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($log->new_values as $field => $value)
                                                    <tr>
                                                        <td><code>{{ $field }}</code></td>
                                                        <td>
                                                            @if(is_array($value) || is_object($value))
                                                                <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                            @elseif(is_bool($value))
                                                                {{ $value ? 'true' : 'false' }}
                                                            @elseif(is_null($value))
                                                                <span class="text-muted">null</span>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="p-3 text-muted">No change details available for this event type.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            @can('delete audit logs')
                                <form action="{{ route('admin.audit-logs.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this log?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete Log
                                    </button>
                                </form>
                            @endcan
                        </div>
                        <div class="text-muted">
                            Last updated: {{ $log->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
