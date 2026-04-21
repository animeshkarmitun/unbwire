@extends('frontend.layouts.master')

@section('title', 'My Dashboard')

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
                        <a href="javascript:;" class="breadcrumbs__url">{{ __('frontend.My Dashboard') }}</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <h2 class="mb-4">{{ __('frontend.My Dashboard') }}</h2>

                    <div class="row">
                        <!-- Sidebar Menu -->
                        <div class="col-md-3">
                            <div class="list-group mb-4">
                                <a href="{{ route('user.dashboard') }}" class="list-group-item list-group-item-action active">
                                    <i class="fas fa-chart-line mr-2"></i> Statistics
                                </a>
                                <a href="{{ route('user.news-list') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-list mr-2"></i> News List
                                </a>
                                <a href="{{ route('user.profile') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                            </div>

                            <div class="list-group mb-4">
                                <div class="list-group-item list-group-item-dark font-weight-bold">
                                    Media Access
                                </div>
                                @if(auth()->user()->canAccessApPhoto())
                                    <a href="{{ route('ap-photo.index') }}" class="list-group-item list-group-item-action">
                                        <i class="fas fa-camera mr-2"></i> AP Photos
                                    </a>
                                @endif
                                
                                @if(auth()->user()->hasSubscriptionAccess('images'))
                                    <a href="{{ route('image-gallery.index') }}" class="list-group-item list-group-item-action">
                                        <i class="fas fa-images mr-2"></i> Image Gallery
                                    </a>
                                @endif

                                @if(auth()->user()->hasSubscriptionAccess('videos'))
                                    <a href="{{ route('video-gallery.index') }}" class="list-group-item list-group-item-action">
                                        <i class="fas fa-video mr-2"></i> Video Gallery
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="col-md-9">
                            <h4 class="mb-3">News Export Statistics</h4>
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body">
                                            <h2 class="mb-0">{{ $stats['total'] }}</h2>
                                            <p class="mb-0">Total Exports</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="card text-center bg-success text-white">
                                        <div class="card-body">
                                            <h2 class="mb-0">{{ $stats['last_30_days'] }}</h2>
                                            <p class="mb-0">Last 30 Days</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="card text-center bg-info text-white">
                                        <div class="card-body">
                                            <h2 class="mb-0">{{ $stats['last_7_days'] }}</h2>
                                            <p class="mb-0">Last 7 Days</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="card text-center bg-warning text-dark">
                                        <div class="card-body">
                                            <h2 class="mb-0">{{ $stats['today'] }}</h2>
                                            <p class="mb-0">Today</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded text-center">
                                                <i class="fas fa-search fa-2x mb-2 text-primary"></i>
                                                <h6>Search News Archive</h6>
                                                <p class="small text-muted">Browse and search previous news articles.</p>
                                                <a href="{{ route('user.news-list') }}" class="btn btn-sm btn-primary">Go to News List</a>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="p-3 border rounded text-center h-100">
                                                <i class="fas fa-file-export fa-2x mb-2 text-success"></i>
                                                <h6>Bulk Export</h6>
                                                <p class="small text-muted">Export up to 10 days of news in a single ZIP.</p>
                                                <a href="{{ route('user.news-list') }}?bulk_export=1" class="btn btn-sm btn-success">Start Export</a>
                                            </div>
                                        </div>
                                        @if(auth()->user()->canAccessApPhoto())
                                        <div class="col-md-6 mb-4">
                                            <div class="p-3 border rounded text-center h-100">
                                                <i class="fas fa-camera fa-2x mb-2 text-info"></i>
                                                <h6>AP Photo Gallery</h6>
                                                <p class="small text-muted">Access premium AP photo collections.</p>
                                                <a href="{{ route('ap-photo.index') }}" class="btn btn-sm btn-info">View AP Photos</a>
                                            </div>
                                        </div>
                                        @endif
                                        @if(auth()->user()->hasSubscriptionAccess('images') || auth()->user()->hasSubscriptionAccess('videos'))
                                        <div class="col-md-6 mb-4">
                                            <div class="p-3 border rounded text-center h-100">
                                                <i class="fas fa-photo-video fa-2x mb-2 text-warning"></i>
                                                <h6>Media Gallery</h6>
                                                <p class="small text-muted">Browse UNB images and videos.</p>
                                                <div class="btn-group">
                                                    @if(auth()->user()->hasSubscriptionAccess('images'))
                                                        <a href="{{ route('image-gallery.index') }}" class="btn btn-sm btn-warning">Images</a>
                                                    @endif
                                                    @if(auth()->user()->hasSubscriptionAccess('videos'))
                                                        <a href="{{ route('video-gallery.index') }}" class="btn btn-sm btn-dark">Videos</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
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
