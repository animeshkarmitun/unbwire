@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>User Subscriptions</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>All User Subscriptions</h4>
                @php
                    $pendingCount = \App\Models\UserSubscription::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <div class="card-header-action">
                        <span class="badge badge-warning badge-lg">{{ $pendingCount }} Pending Approval</span>
                    </div>
                @endif
            </div>

            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="{{ route('admin.user-subscription.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Filter by Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Filter by Package</label>
                                <select name="package_id" class="form-control">
                                    <option value="">All Packages</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>User</th>
                                <th>Package</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>Expiry Date</th>
                                <th>Days Remaining</th>
                                <th>Payment Method</th>
                                <th>Auto Renew</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr class="{{ $subscription->status == 'pending' ? 'table-warning' : '' }}">
                                    <td>{{ $subscription->id }}</td>
                                    <td>
                                        <strong>{{ $subscription->user->name }}</strong><br>
                                        <small class="text-muted">{{ $subscription->user->email }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $subscription->package->name }}</strong><br>
                                        <small class="text-muted">{{ $subscription->package->currency }} {{ number_format($subscription->package->price, 2) }}/{{ $subscription->package->billing_period }}</small>
                                    </td>
                                    <td>
                                        @if($subscription->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @elseif($subscription->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($subscription->status == 'expired')
                                            <span class="badge badge-danger">Expired</span>
                                        @elseif($subscription->status == 'cancelled')
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @else
                                            <span class="badge badge-info">{{ ucfirst($subscription->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $subscription->starts_at->format('M d, Y') }}</td>
                                    <td>{{ $subscription->expires_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($subscription->isActive())
                                            <span class="badge badge-info">{{ $subscription->daysRemaining() }} days</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($subscription->payment_method ?? 'N/A') }}</td>
                                    <td>
                                        @if($subscription->auto_renew)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn {{ $subscription->status == 'pending' ? 'btn-success' : 'btn-primary' }} btn-sm dropdown-toggle" 
                                                    data-toggle="dropdown">
                                                <i class="fas fa-cog"></i> Actions
                                            </button>
                                            <div class="dropdown-menu">
                                                @if($subscription->status != 'active')
                                                    <form action="{{ route('admin.user-subscription.update', $subscription->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit" class="dropdown-item {{ $subscription->status == 'pending' ? 'font-weight-bold text-success' : '' }}" 
                                                                onclick="return confirm('{{ $subscription->status == 'pending' ? 'Approve and activate this subscription?' : 'Mark this subscription as active?' }}')">
                                                            <i class="fas fa-check-circle text-success"></i> {{ $subscription->status == 'pending' ? 'Approve & Activate' : 'Mark as Active' }}
                                                        </button>
                                                    </form>
                                                    @if($subscription->status == 'pending')
                                                        <div class="dropdown-divider"></div>
                                                    @endif
                                                @endif
                                                @if($subscription->status != 'expired')
                                                    <form action="{{ route('admin.user-subscription.update', $subscription->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="expired">
                                                        <button type="submit" class="dropdown-item" onclick="return confirm('Mark this subscription as expired?')">
                                                            <i class="fas fa-clock text-warning"></i> Mark as Expired
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($subscription->status != 'cancelled')
                                                    <form action="{{ route('admin.user-subscription.update', $subscription->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="dropdown-item" onclick="return confirm('Cancel this subscription?')">
                                                            <i class="fas fa-times text-danger"></i> Cancel
                                                        </button>
                                                    </form>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#updateExpiryDateModal{{ $subscription->id }}">
                                                    <i class="fas fa-calendar-alt text-info"></i> Update Expiry Date
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a href="{{ route('admin.user-subscription.destroy', $subscription->id) }}" 
                                                   class="dropdown-item text-danger delete-item">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No subscriptions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $subscriptions->links() }}
                </div>
            </div>
        </div>
    </section>

    <!-- Update Expiry Date Modals -->
    @foreach($subscriptions as $subscription)
        <div class="modal fade" id="updateExpiryDateModal{{ $subscription->id }}" tabindex="-1" role="dialog" aria-labelledby="updateExpiryDateModalLabel{{ $subscription->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateExpiryDateModalLabel{{ $subscription->id }}">Update Expiry Date</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.user-subscription.update-expiry-date', $subscription->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label>User</label>
                                <input type="text" class="form-control" value="{{ $subscription->user->name }} ({{ $subscription->user->email }})" readonly>
                            </div>
                            <div class="form-group">
                                <label>Package</label>
                                <input type="text" class="form-control" value="{{ $subscription->package->name }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Current Expiry Date</label>
                                <input type="text" class="form-control" value="{{ $subscription->expires_at->format('M d, Y') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="expires_at{{ $subscription->id }}">New Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('expires_at') is-invalid @enderror" 
                                       id="expires_at{{ $subscription->id }}" 
                                       name="expires_at" 
                                       value="{{ old('expires_at', $subscription->expires_at->format('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                       required>
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Please select a future date.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Expiry Date</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        $("#table").dataTable({
            "columnDefs": [{
                "sortable": false,
                "targets": [9]
            }]
        });
    </script>
@endpush

