@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Activity Log Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.activity-log.index') }}">Activity Logs</a></div>
            <div class="breadcrumb-item active">Log #{{ $log->id }}</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Log Information</h4>
                        @if($log->action == 'deleted' && $log->canBeRestored())
                        <div class="card-header-action">
                            <form action="{{ route('admin.activity-log.restore', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to restore this item?');">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-undo"></i> Restore Item
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="200">Log ID:</th>
                                        <td>{{ $log->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Action:</th>
                                        <td>
                                            <span class="badge badge-{{ $log->action == 'created' ? 'success' : ($log->action == 'updated' ? 'warning' : ($log->action == 'deleted' ? 'danger' : 'info')) }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Model Type:</th>
                                        <td>{{ $log->model_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Model ID:</th>
                                        <td>{{ $log->model_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>User:</th>
                                        <td>
                                            @if($log->user)
                                                {{ $log->user->name }} ({{ $log->user_type }})
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($log->created_by)
                                    <tr>
                                        <th>Created By:</th>
                                        <td>
                                            @if($log->createdBy)
                                                {{ $log->createdBy->name }} ({{ $log->created_by_type }})
                                            @else
                                                ID: {{ $log->created_by }} ({{ $log->created_by_type }})
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($log->updated_by)
                                    <tr>
                                        <th>Updated By:</th>
                                        <td>
                                            @if($log->updatedBy)
                                                {{ $log->updatedBy->name }} ({{ $log->updated_by_type }})
                                            @else
                                                ID: {{ $log->updated_by }} ({{ $log->updated_by_type }})
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($log->approve_by)
                                    <tr>
                                        <th>Approved By:</th>
                                        <td>
                                            @if($log->approvedBy)
                                                {{ $log->approvedBy->name }} ({{ $log->approve_by_type }})
                                            @else
                                                ID: {{ $log->approve_by }} ({{ $log->approve_by_type }})
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Description:</th>
                                        <td>{{ $log->description }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date & Time:</th>
                                        <td>{{ $log->created_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="200">IP Address:</th>
                                        <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>User Agent:</th>
                                        <td>{{ Str::limit($log->user_agent ?? 'N/A', 100) }}</td>
                                    </tr>
                                    <tr>
                                        <th>URL:</th>
                                        <td>{{ $log->url ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Method:</th>
                                        <td>{{ $log->method ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($log->action == 'updated' && $log->changes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Changes Made</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Field</th>
                                                <th>Old Value</th>
                                                <th>New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($log->changes as $field => $newValue)
                                            <tr>
                                                <td><strong>{{ $field }}</strong></td>
                                                <td>
                                                    @php
                                                        $oldValue = $log->old_values[$field] ?? null;
                                                    @endphp
                                                    @if(is_array($oldValue))
                                                        <pre class="mb-0">{{ json_encode($oldValue, JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ $oldValue ?? 'N/A' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(is_array($newValue))
                                                        <pre class="mb-0">{{ json_encode($newValue, JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ $newValue ?? 'N/A' }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($log->old_values || $log->new_values)
                        <div class="row mt-4">
                            <div class="col-md-6">
                                @if($log->old_values)
                                <h5>Old Values</h5>
                                <pre class="bg-light p-3" style="max-height: 400px; overflow-y: auto;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($log->new_values)
                                <h5>New Values</h5>
                                <pre class="bg-light p-3" style="max-height: 400px; overflow-y: auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

