@extends('frontend.layouts.master')

@section('title', 'UNB Video Gallery')

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
                        <a href="javascript:;" class="breadcrumbs__url">UNB Video Gallery</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0">UNB Video Gallery</h2>
                            <p class="text-muted">Broadcast and news videos from around the world.</p>
                        </div>
                    </div>

                    <div class="row">
                        @forelse($videos as $video)
                            <div class="col-md-6 mb-4">
                                <div class="card video-card border-0 shadow-sm h-100">
                                    <div class="video-container position-relative overflow-hidden" 
                                         style="height: 300px; background: #000; border-radius: 8px 8px 0 0;">
                                        @if($video->isExternalVideo())
                                            <iframe width="100%" height="100%" 
                                                    src="{{ $video->embed_url }}" 
                                                    frameborder="0" 
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen></iframe>
                                        @elseif($video->media)
                                            <video controls style="width: 100%; height: 100%; object-fit: contain;">
                                                <source src="{{ asset($video->media->file_url) }}" type="{{ $video->media->mime_type }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title text-dark">{{ $video->title ?: 'News Video' }}</h5>
                                        <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($video->description, 120) }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-play mr-1"></i> {{ ucfirst($video->video_platform ?: 'Local') }}
                                            </span>
                                            <span class="small text-muted">
                                                <i class="far fa-calendar-alt mr-1"></i> {{ $video->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-video-slash fa-4x text-muted mb-3"></i>
                                <h3>No Videos Found</h3>
                                <p class="text-muted">Our video archive is currently empty. Please check back soon.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-center">
                            {{ $videos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .video-card {
        transition: transform 0.3s ease;
    }
    .video-card:hover {
        transform: scale(1.01);
    }
</style>
@endsection
