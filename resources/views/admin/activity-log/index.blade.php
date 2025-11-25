@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Activity Logs</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Activity Logs</div>
        </div>
        <div class="section-header-action">
            <a href="{{ route('admin.activity-log.settings') }}" class="btn btn-primary ml-2">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>
    
    <div class="section-body">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-12 mb-3">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.activity-log.index', ['user_type' => 'admin']) }}" 
                       class="btn {{ request('user_type', 'admin') == 'admin' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-user-shield"></i> Admin Activities
                        <span class="badge badge-light ml-2">{{ $adminStats['total_activities'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.activity-log.index', ['user_type' => 'user']) }}" 
                       class="btn {{ request('user_type') == 'user' ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="fas fa-users"></i> User Activities
                        <span class="badge badge-light ml-2">{{ $userStats['total_activities'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.activity-log.index') }}" 
                       class="btn {{ !request()->has('user_type') ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="fas fa-list"></i> All Activities
                        <span class="badge badge-light ml-2">{{ ($adminStats['total_activities'] ?? 0) + ($userStats['total_activities'] ?? 0) }}</span>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Activities (30 days)</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['total_activities'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            @if(request('user_type', 'admin') == 'admin')
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Created</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['created'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Updated</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['updated'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-trash"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Deleted</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['deleted'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Viewed</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['viewed'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Exported</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['exported'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Commented</h4>
                        </div>
                        <div class="card-body">
                            {{ $statistics['commented'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Filters and Logs Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Activity Logs</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.activity-log.deleted') }}" class="btn btn-warning">
                                <i class="fas fa-undo"></i> Deleted Items
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="mb-4">
                            <input type="hidden" name="user_type" value="{{ request('user_type', 'admin') }}">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>User Type</label>
                                        <select name="user_type" class="form-control" onchange="this.form.submit()">
                                            <option value="admin" {{ request('user_type', 'admin') == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="user" {{ request('user_type') == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="" {{ !request()->has('user_type') ? 'selected' : '' }}>All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Action</label>
                                        <select name="action" class="form-control">
                                            <option value="">All Actions</option>
                                            @if(request('user_type', 'admin') == 'admin')
                                            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                                            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                                            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                            <option value="restored" {{ request('action') == 'restored' ? 'selected' : '' }}>Restored</option>
                                            @else
                                            <option value="viewed" {{ request('action') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                            <option value="exported" {{ request('action') == 'exported' ? 'selected' : '' }}>Exported</option>
                                            <option value="commented" {{ request('action') == 'commented' ? 'selected' : '' }}>Commented</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Model Type</label>
                                        <select name="model_type" class="form-control">
                                            <option value="">All Models</option>
                                            @foreach($modelTypes as $type)
                                            <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Date From</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Date To</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @if(request()->hasAny(['action', 'model_type', 'date_from', 'date_to', 'search']))
                            <a href="{{ route('admin.activity-log.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                            @endif
                        </form>

                        <!-- Logs Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Model</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            @if($log->user)
                                                @php
                                                    $roleName = null;
                                                    if ($log->user_type === 'admin' && $log->user instanceof \App\Models\Admin) {
                                                        try {
                                                            $roleName = $log->user->getRoleNames()->first();
                                                        } catch (\Exception $e) {
                                                            // Role not available
                                                        }
                                                    }
                                                @endphp
                                                @if($log->user_type === 'admin')
                                                    <span class="badge badge-primary mb-1">Admin</span>
                                                    @if($roleName)
                                                        <br><strong>{{ $roleName }}</strong>
                                                        <br><small class="text-muted">{{ $log->user->name }}</small>
                                                    @else
                                                        <br>{{ $log->user->name }}
                                                    @endif
                                                @else
                                                    <span class="badge badge-success mb-1">User</span>
                                                    <br>{{ $log->user->name }}
                                                @endif
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                            @if($log->created_by && $log->action == 'created')
                                                <br><small class="text-success">Created by: {{ $log->createdBy->name ?? 'ID: ' . $log->created_by }}</small>
                                            @endif
                                            @if($log->updated_by && $log->action == 'updated')
                                                <br><small class="text-warning">Updated by: {{ $log->updatedBy->name ?? 'ID: ' . $log->updated_by }}</small>
                                            @endif
                                            @if($log->approve_by)
                                                <br><small class="text-info">Approved by: {{ $log->approvedBy->name ?? 'ID: ' . $log->approve_by }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $log->action == 'created' ? 'success' : ($log->action == 'updated' ? 'warning' : ($log->action == 'deleted' ? 'danger' : 'info')) }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ class_basename($log->model_type) }}</small>
                                            @if($log->model_id)
                                                <br><span class="text-muted">ID: {{ $log->model_id }}</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($log->description, 50) }}</td>
                                        <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.activity-log.show', $log->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No activity logs found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

