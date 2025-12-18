@extends('frontend.layouts.master')

@section('title', 'My Profile')

@section('content')
<section class="pb-80">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Breadcrumb -->
                <ul class="breadcrumbs bg-light mb-4">
                    <li class="breadcrumbs__item">
                        <a href="{{ url('/') }}" class="breadcrumbs__url">
                            <i class="fa fa-home"></i> {{ __('frontend.Home') }}</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="javascript:;" class="breadcrumbs__url">My Profile</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <h2 class="mb-4">My Profile</h2>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Profile Tabs -->
                    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="subscription-tab" data-toggle="tab" href="#subscription" role="tab" aria-controls="subscription" aria-selected="true">
                                <i class="fas fa-crown"></i> Subscription
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">
                                <i class="fas fa-lock"></i> Change Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="package-tab" data-toggle="tab" href="#package" role="tab" aria-controls="package" aria-selected="false">
                                <i class="fas fa-exchange-alt"></i> Change Package
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabsContent">
                        <!-- Subscription Tab -->
                        <div class="tab-pane fade show active" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
                            @if($pendingSubscription)
                                <div class="card mb-4">
                                    <div class="card-header bg-warning text-dark">
                                        <h4 class="mb-0"><i class="fas fa-clock"></i> Pending Subscription</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>{{ $pendingSubscription->package->name }}</h5>
                                                <p class="text-muted">{{ $pendingSubscription->package->description }}</p>
                                                
                                                <div class="mt-3">
                                                    <strong>Status:</strong>
                                                    <span class="badge badge-warning">Pending</span>
                                                </div>

                                                <div class="mt-2">
                                                    <strong>Applied On:</strong> 
                                                    {{ $pendingSubscription->created_at->format('M d, Y') }}
                                                </div>

                                                <div class="mt-2">
                                                    <strong>Expected Start:</strong> 
                                                    {{ $pendingSubscription->starts_at->format('M d, Y') }}
                                                </div>

                                                <div class="mt-2">
                                                    <strong>Expected Expiry:</strong> 
                                                    {{ $pendingSubscription->expires_at->format('M d, Y') }}
                                                </div>

                                                <div class="mt-2">
                                                    <strong>Payment Method:</strong>
                                                    <span class="badge badge-info">{{ ucfirst($pendingSubscription->payment_method ?? 'N/A') }}</span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    <strong>Your subscription request is pending admin approval.</strong>
                                                    <p class="mb-0 mt-2">You will be notified once your subscription is activated. Please do not apply for another subscription until this one is processed.</p>
                                                </div>

                                                <h6>Features Included:</h6>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check text-success"></i> News Articles</li>
                                                    @if($pendingSubscription->package->access_images)
                                                        <li><i class="fas fa-check text-success"></i> High-Quality Images</li>
                                                    @endif
                                                    @if($pendingSubscription->package->access_videos)
                                                        <li><i class="fas fa-check text-success"></i> Video Content</li>
                                                    @endif
                                                    @if($pendingSubscription->package->access_exclusive)
                                                        <li><i class="fas fa-check text-success"></i> Exclusive Articles</li>
                                                    @endif
                                                    @if($pendingSubscription->package->ad_free)
                                                        <li><i class="fas fa-check text-success"></i> Ad-Free Experience</li>
                                                    @endif
                                                    @if($pendingSubscription->package->priority_support)
                                                        <li><i class="fas fa-check text-success"></i> Priority Support</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($activeSubscription && $activeSubscription->isActive())
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h4 class="mb-0">Current Subscription</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>{{ $activeSubscription->package->name }}</h5>
                                                <p class="text-muted">{{ $activeSubscription->package->description }}</p>
                                                
                                                <div class="mt-3">
                                                    <strong>Status:</strong>
                                                    <span class="badge badge-success">Active</span>
                                                </div>

                                                <div class="mt-2">
                                                    <strong>Started:</strong> 
                                                    {{ $activeSubscription->starts_at->format('M d, Y') }}
                                                </div>

                                                <div class="mt-2">
                                                    <strong>Expires:</strong> 
                                                    {{ $activeSubscription->expires_at->format('M d, Y') }}
                                                </div>

                                                <div class="mt-2">
                                                    <strong>Days Remaining:</strong> 
                                                    <span class="badge badge-info">{{ $activeSubscription->daysRemaining() }} days</span>
                                                </div>

                                                <div class="mt-2">
                                                    <strong>Auto-Renew:</strong>
                                                    @if($activeSubscription->auto_renew)
                                                        <span class="badge badge-success">Enabled</span>
                                                    @else
                                                        <span class="badge badge-warning">Disabled</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <h6>Features Included:</h6>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check text-success"></i> News Articles</li>
                                                    @if($activeSubscription->package->access_images)
                                                        <li><i class="fas fa-check text-success"></i> High-Quality Images</li>
                                                    @endif
                                                    @if($activeSubscription->package->access_videos)
                                                        <li><i class="fas fa-check text-success"></i> Video Content</li>
                                                    @endif
                                                    @if($activeSubscription->package->access_exclusive)
                                                        <li><i class="fas fa-check text-success"></i> Exclusive Articles</li>
                                                    @endif
                                                    @if($activeSubscription->package->ad_free)
                                                        <li><i class="fas fa-check text-success"></i> Ad-Free Experience</li>
                                                    @endif
                                                    @if($activeSubscription->package->priority_support)
                                                        <li><i class="fas fa-check text-success"></i> Priority Support</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif(!$pendingSubscription)
                                <div class="alert alert-warning">
                                    <h5>No Active Subscription</h5>
                                    <p>You don't have an active subscription. Please subscribe to access our premium news content.</p>
                                    <a href="{{ route('subscription.plans') }}" class="btn btn-primary">
                                        View Subscription Plans
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">Change Password</h4>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('user.profile.password.update') }}">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label for="current_password">Current Password</label>
                                            <input type="password" 
                                                   class="form-control @error('current_password') is-invalid @enderror" 
                                                   id="current_password" 
                                                   name="current_password" 
                                                   required>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="password">New Password</label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm New Password</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   required>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Change Package Tab -->
                        <div class="tab-pane fade" id="package" role="tabpanel" aria-labelledby="package-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">Change Subscription Package</h4>
                                </div>
                                <div class="card-body">
                                    @if($pendingSubscription)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>You have a pending subscription request.</strong>
                                            <p class="mb-0 mt-2">Please wait for admin approval of your current subscription request before changing your package.</p>
                                        </div>
                                    @elseif($activeSubscription && $activeSubscription->isActive())
                                        <div class="alert alert-info">
                                            <strong>Note:</strong> You currently have an active subscription. 
                                            Your package change will take effect after your current subscription expires on 
                                            <strong>{{ $activeSubscription->expires_at->format('M d, Y') }}</strong>.
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <strong>Note:</strong> You don't have an active subscription. 
                                            The package change will take effect immediately after admin approval.
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('user.profile.package.change') }}">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label for="package_id">Select Package</label>
                                            <select class="form-control @error('package_id') is-invalid @enderror" 
                                                    id="package_id" 
                                                    name="package_id" 
                                                    {{ $pendingSubscription ? 'disabled' : 'required' }}>
                                                <option value="">-- Select a Package --</option>
                                                @foreach($packages as $package)
                                                    <option value="{{ $package->id }}" 
                                                            {{ $currentPackage && $currentPackage->id == $package->id ? 'selected' : '' }}>
                                                        {{ $package->name }} - 
                                                        ${{ number_format($package->price, 2) }}/{{ $package->billing_period }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('package_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary" {{ $pendingSubscription ? 'disabled' : '' }}>
                                            <i class="fas fa-exchange-alt"></i> Change Package
                                        </button>
                                    </form>

                                    <div class="mt-4">
                                        <h5>Available Packages:</h5>
                                        <div class="row">
                                            @foreach($packages as $package)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h6>{{ $package->name }}</h6>
                                                            <p class="text-muted small">{{ $package->description }}</p>
                                                            <p class="h5">${{ number_format($package->price, 2) }}/{{ $package->billing_period }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

