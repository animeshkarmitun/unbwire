@extends('frontend.layouts.master')

@section('title', 'My Subscription')

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
                        <a href="javascript:;" class="breadcrumbs__url">My Subscription</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <h2 class="mb-4">My Subscription</h2>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($activeSubscription)
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
                                            @if($activeSubscription->isActive())
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Expired</span>
                                            @endif
                                        </div>

                                        <div class="mt-2">
                                            <strong>Started:</strong> 
                                            {{ $activeSubscription->starts_at->format('M d, Y') }}
                                        </div>

                                        <div class="mt-2">
                                            <strong>Expires:</strong> 
                                            {{ $activeSubscription->expires_at->format('M d, Y') }}
                                        </div>

                                        @if($activeSubscription->isActive())
                                            <div class="mt-2">
                                                <strong>Days Remaining:</strong> 
                                                <span class="badge badge-info">{{ $activeSubscription->daysRemaining() }} days</span>
                                            </div>
                                        @endif

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

                                        @if($activeSubscription->isActive() && $activeSubscription->auto_renew)
                                            <form action="{{ route('subscription.cancel') }}" method="POST" class="mt-3">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm" 
                                                        onclick="return confirm('Are you sure you want to cancel auto-renewal?')">
                                                    Cancel Auto-Renewal
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h5>No Active Subscription</h5>
                            <p>You don't have an active subscription. Please subscribe to access our premium news content.</p>
                            <a href="{{ route('subscription.plans') }}" class="btn btn-primary">
                                View Subscription Plans
                            </a>
                        </div>
                    @endif

                    @if($subscriptionHistory && $subscriptionHistory->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Subscription History</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Package</th>
                                                <th>Status</th>
                                                <th>Start Date</th>
                                                <th>Expiry Date</th>
                                                <th>Payment Method</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subscriptionHistory as $subscription)
                                                <tr>
                                                    <td>{{ $subscription->package->name }}</td>
                                                    <td>
                                                        @if($subscription->status == 'active')
                                                            <span class="badge badge-success">Active</span>
                                                        @elseif($subscription->status == 'expired')
                                                            <span class="badge badge-danger">Expired</span>
                                                        @elseif($subscription->status == 'cancelled')
                                                            <span class="badge badge-warning">Cancelled</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ ucfirst($subscription->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $subscription->starts_at->format('M d, Y') }}</td>
                                                    <td>{{ $subscription->expires_at->format('M d, Y') }}</td>
                                                    <td>{{ ucfirst($subscription->payment_method ?? 'N/A') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('subscription.plans') }}" class="btn btn-primary">
                            <i class="fas fa-crown"></i> View All Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

