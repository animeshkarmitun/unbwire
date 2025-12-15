@extends('frontend.layouts.master')

@section('title', 'Subscription Plans')

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
                        <a href="javascript:;" class="breadcrumbs__url">Subscription Plans</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <h2 class="mb-4">Choose Your Subscription Plan</h2>
                    <p class="mb-5">Select the plan that best fits your needs to access our premium news content.</p>

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

                    @auth
                        @if($latestSubscription)
                            <div class="alert alert-info mb-4">
                                <h5 class="mb-2"><i class="fas fa-info-circle"></i> Your Current Subscription Status</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Package:</strong> {{ $latestSubscription->package->name }}</p>
                                        <p class="mb-1"><strong>Status:</strong> 
                                            @if($latestSubscription->status == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @elseif($latestSubscription->status == 'pending')
                                                <span class="badge badge-warning">Pending Approval</span>
                                            @elseif($latestSubscription->status == 'expired')
                                                <span class="badge badge-danger">Expired</span>
                                            @elseif($latestSubscription->status == 'cancelled')
                                                <span class="badge badge-secondary">Cancelled</span>
                                            @else
                                                <span class="badge badge-info">{{ ucfirst($latestSubscription->status) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        @if($latestSubscription->status == 'active')
                                            <p class="mb-1"><strong>Expires:</strong> {{ $latestSubscription->expires_at->format('M d, Y') }}</p>
                                            <p class="mb-0"><strong>Days Remaining:</strong> {{ $latestSubscription->daysRemaining() }} days</p>
                                        @elseif($latestSubscription->status == 'pending')
                                            <p class="mb-0"><strong>Submitted:</strong> {{ $latestSubscription->created_at->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($latestSubscription->status == 'pending')
                                    <div class="mt-3">
                                        <p class="mb-0 text-warning"><i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> You cannot subscribe to a new plan while your subscription request is pending admin approval.</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endauth

                    <div class="row mt-4">
                        @foreach($packages as $package)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm {{ $userSubscription && $userSubscription->package_id == $package->id ? 'border-primary' : '' }}">
                                    <div class="card-header bg-primary text-white text-center">
                                        <h3 class="mb-0">{{ $package->name }}</h3>
                                        @if($userSubscription && $userSubscription->subscription_package_id == $package->id)
                                            <span class="badge badge-light">Current Plan</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-4">
                                            <h2 class="display-4">
                                                <span class="text-primary">{{ $package->currency }} {{ number_format($package->price, 2) }}</span>
                                            </h2>
                                            <p class="text-muted">per {{ $package->billing_period }}</p>
                                        </div>

                                        @if($package->description)
                                            <p class="text-muted mb-4">{{ $package->description }}</p>
                                        @endif

                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success"></i> 
                                                <strong>News Articles</strong>
                                            </li>
                                            @if($package->access_images)
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success"></i> 
                                                    <strong>High-Quality Images</strong>
                                                </li>
                                            @else
                                                <li class="mb-2 text-muted">
                                                    <i class="fas fa-times"></i> Images
                                                </li>
                                            @endif
                                            @if($package->access_videos)
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success"></i> 
                                                    <strong>Video Content</strong>
                                                </li>
                                            @else
                                                <li class="mb-2 text-muted">
                                                    <i class="fas fa-times"></i> Videos
                                                </li>
                                            @endif
                                            @if($package->access_exclusive)
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success"></i> 
                                                    <strong>Exclusive Articles</strong>
                                                </li>
                                            @else
                                                <li class="mb-2 text-muted">
                                                    <i class="fas fa-times"></i> Exclusive Content
                                                </li>
                                            @endif
                                            @if($package->ad_free)
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success"></i> 
                                                    <strong>Ad-Free Experience</strong>
                                                </li>
                                            @endif
                                            @if($package->priority_support)
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success"></i> 
                                                    <strong>Priority Support</strong>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        @auth
                                            @if($userSubscription && $userSubscription->subscription_package_id == $package->id)
                                                <button class="btn btn-secondary btn-block" disabled>
                                                    Current Plan
                                                </button>
                                            @elseif($hasPendingSubscription)
                                                <button class="btn btn-warning btn-block" disabled>
                                                    <i class="fas fa-clock"></i> Pending Approval
                                                </button>
                                                <small class="text-muted d-block mt-2 text-center">Please wait for admin approval</small>
                                            @else
                                                <a href="{{ route('subscription.checkout', $package->id) }}" class="btn btn-primary btn-block">
                                                    @if($userSubscription)
                                                        Switch Plan
                                                    @else
                                                        Subscribe Now
                                                    @endif
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-block">
                                                Login to Subscribe
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @auth
                        <div class="mt-4 text-center">
                            <a href="{{ route('subscription.my-subscription') }}" class="btn btn-outline-primary">
                                View My Subscription
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

