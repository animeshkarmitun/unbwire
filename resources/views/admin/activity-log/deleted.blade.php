@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Deleted Items</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.activity-log.index') }}">Activity Logs</a></div>
            <div class="breadcrumb-item active">Deleted Items</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Deleted Items (Can be Restored)</h4>
                        <div class="card-header-form">
                            <form method="GET" action="{{ route('admin.activity-log.deleted') }}" class="form-inline">
                                <select name="model_type" class="form-control mr-2">
                                    <option value="">All Models</option>
                                    @foreach($modelTypes as $type)
                                    <option value="{{ $type }}" {{ $modelType == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Model Type</th>
                                        <th>Model ID</th>
                                        <th>Deleted By</th>
                                        <th>Deleted At</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deletedItems as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            <strong>{{ class_basename($log->model_type) }}</strong>
                                        </td>
                                        <td>{{ $log->model_id ?? 'N/A' }}</td>
                                        <td>
                                            @if($log->user)
                                                {{ $log->user->name }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ Str::limit($log->description, 50) }}</td>
                                        <td>
                                            <a href="{{ route('admin.activity-log.show', $log->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if($log->canBeRestored())
                                            <form action="{{ route('admin.activity-log.restore', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to restore this item?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-undo"></i> Restore
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No deleted items found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


