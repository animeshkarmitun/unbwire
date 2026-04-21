@extends('frontend.layouts.master')

@section('title', 'News Archive')

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
                        <a href="{{ route('user.dashboard') }}" class="breadcrumbs__url">{{ __('frontend.My Dashboard') }}</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="javascript:;" class="breadcrumbs__url">News List</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2 class="mb-0">News Archive</h2>
                        </div>
                        <div class="col-md-6 text-right">
                            @if(request()->filled('from_date') && request()->filled('to_date'))
                                <a href="{{ route('user.news-list.export-zip', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" class="btn btn-success">
                                    <i class="fas fa-file-archive"></i> Export All as ZIP
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <!-- Sidebar Menu -->
                        <div class="col-md-3">
                            <div class="list-group mb-4">
                                <a href="{{ route('user.dashboard') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-chart-line mr-2"></i> Statistics
                                </a>
                                <a href="{{ route('user.news-list') }}" class="list-group-item list-group-item-action active">
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

                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-search"></i> Search Archive
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('user.news-list') }}" method="GET">
                                        <div class="form-group">
                                            <label>From Date</label>
                                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>To Date</label>
                                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">Search</button>
                                        <p class="small text-muted mt-2"><i class="fas fa-info-circle"></i> Max 10 days window allowed.</p>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="col-md-9">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        @if(request()->filled('from_date'))
                                            Results for {{ Carbon\Carbon::parse(request('from_date'))->format('M d, Y') }} - {{ Carbon\Carbon::parse(request('to_date'))->format('M d, Y') }}
                                        @else
                                            Today's News ({{ now()->format('M d, Y') }})
                                        @endif
                                    </h5>
                                    <span class="badge badge-primary">{{ $news->total() }} Articles</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Image</th>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($news as $article)
                                                    <tr>
                                                        <td style="width: 80px;">
                                                            <img src="{{ asset($article->image) }}" class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('news-details', $article->slug) }}" target="_blank" class="text-dark font-weight-bold">
                                                                {!! truncate($article->title, 60) !!}
                                                            </a>
                                                        </td>
                                                        <td>{{ $article->category->name }}</td>
                                                        <td>{{ $article->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('news.export', ['slug' => $article->slug, 'format' => 'pdf']) }}" class="btn btn-sm btn-outline-danger" title="Export PDF">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                                <a href="{{ route('news.export', ['slug' => $article->slug, 'format' => 'txt']) }}" class="btn btn-sm btn-outline-secondary" title="Export Text">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">No news articles found for this range.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if($news->hasPages())
                                    <div class="card-footer">
                                        {{ $news->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
