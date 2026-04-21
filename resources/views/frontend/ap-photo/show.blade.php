@extends('frontend.layouts.master')

@section('title', ($photo->category->name ?? 'AP Photo') . ' - AP Photo Gallery')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ap-photo.index') }}">AP Photo Gallery</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $photo->category->name ?? 'Photo Details' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Photo Player / Gallery -->
        <div class="col-lg-8">
            <div id="apPhotoCarousel" class="carousel slide bg-dark rounded overflow-hidden shadow-lg" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($photo->items as $item)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="d-flex align-items-center justify-content-center min-vh-50">
                                <img src="{{ $item->file_url }}" class="d-block w-100 img-fluid" alt="{{ $photo->category->name }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($photo->items->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#apPhotoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#apPhotoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>

            <!-- Thumbnails -->
            <div class="row mt-3 g-2">
                @foreach($photo->items as $item)
                    <div class="col-3 col-md-2">
                        <div class="thumbnail-wrapper rounded overflow-hidden cursor-pointer {{ $loop->first ? 'active' : '' }}" 
                             onclick="$('#apPhotoCarousel').carousel({{ $loop->index }})">
                            <img src="{{ $item->file_url }}" class="img-fluid w-100 h-100 object-fit-cover" alt="thumb">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Details -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card border-0 shadow-sm border-top-primary">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $photo->category->name ?? 'Uncategorized' }}</span>
                        @if($photo->subCategory)
                            <span class="badge bg-info">{{ $photo->subCategory->name }}</span>
                        @endif
                    </div>
                    <h3 class="mb-3">{{ $photo->category->name }}</h3>
                    <div class="text-muted small mb-3">
                        <i class="far fa-calendar-alt me-1"></i> {{ $photo->created_at->format('M d, Y') }}
                        <span class="mx-2">|</span>
                        <i class="far fa-user me-1"></i> {{ $photo->user->name ?? 'Admin' }}
                    </div>
                    
                    <div class="description mb-4">
                        <p>{{ $photo->description ?? 'No description available for this photo set.' }}</p>
                    </div>

                    @if($photo->tags->isNotEmpty())
                        <div class="tags mb-4">
                            <h6 class="mb-2">Tags:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($photo->tags as $tag)
                                    <a href="{{ route('ap-photo.index', ['tag' => $tag->slug]) }}" class="badge rounded-pill bg-light text-dark border text-decoration-none shadow-sm">#{{ $tag->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('ap-photo.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Gallery
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-vh-50 {
        min-height: 50vh;
    }
    .object-fit-cover {
        object-fit: cover;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .thumbnail-wrapper {
        border: 2px solid transparent;
        transition: border-color 0.3s;
        aspect-ratio: 1 / 1;
    }
    .thumbnail-wrapper.active, .thumbnail-wrapper:hover {
        border-color: var(--colorPrimary);
    }
    .border-top-primary {
        border-top: 4px solid var(--colorPrimary) !important;
    }
</style>
@endsection
