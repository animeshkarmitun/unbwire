@extends('frontend.layouts.master')

@section('title', 'UNB Image Gallery')

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
                        <a href="javascript:;" class="breadcrumbs__url">UNB Image Gallery</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="wrap__about-us">
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0">UNB Image Gallery</h2>
                            <p class="text-muted">Exclusive photo collections from United News of Bangladesh.</p>
                        </div>
                    </div>

                    <div class="row">
                        @forelse($galleries as $gallery)
                            <div class="col-md-4 mb-4">
                                <div class="card gallery-card h-100 border-0 shadow-sm overflow-hidden">
                                    <div class="position-relative">
                                        <img src="{{ asset($gallery->cover_image ?: 'frontend/assets/images/placeholder.webp') }}" 
                                             class="card-img-top" 
                                             alt="{{ $gallery->title }}"
                                             style="height: 220px; object-fit: cover;">
                                        <div class="gallery-badge badge badge-primary position-absolute" style="top: 15px; right: 15px;">
                                            <i class="fas fa-camera mr-1"></i> {{ $gallery->photo_count }} Photos
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title text-dark mb-2">{{ $gallery->title ?: 'Photo Collection' }}</h5>
                                        <p class="card-text text-muted small mb-3">
                                            {{ \Illuminate\Support\Str::limit($gallery->description, 100) }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small text-muted">
                                                <i class="far fa-calendar-alt mr-1"></i> {{ $gallery->created_at->format('M d, Y') }}
                                            </span>
                                            <a href="{{ route('ap-photo.index', ['search' => $gallery->gallery_slug]) }}" class="btn btn-sm btn-outline-primary">
                                                View Collection
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-images fa-4x text-muted mb-3"></i>
                                <h3>No Image Galleries Found</h3>
                                <p class="text-muted">We haven't added any public photo collections yet. Please check back later.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-center">
                            {{ $galleries->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .gallery-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .gallery-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .gallery-badge {
        padding: 5px 10px;
        font-weight: 500;
        backdrop-filter: blur(4px);
        background: rgba(var(--primary-color-rgb, 0, 123, 255), 0.9);
    }
</style>
@endsection
