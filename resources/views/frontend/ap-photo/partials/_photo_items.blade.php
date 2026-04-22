@foreach($photos as $item)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border-0 shadow-sm photo-card transition-hover" style="border-radius: 12px; overflow: hidden;">
            <a href="{{ route('ap-photo.show', $item->id) }}" class="text-decoration-none text-dark">
                <div class="ratio ratio-4x3 overflow-hidden">
                    <img src="{{ asset($item->file_url) }}" 
                         class="card-img-top object-fit-cover" 
                         alt="{{ $item->apPhoto->category->name ?? 'AP Photo' }}"
                         style="transition: transform 0.5s ease;">
                    <div class="photo-overlay d-flex align-items-center justify-content-center">
                        <span class="btn btn-light btn-sm rounded-pill px-3">View Details</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge badge-primary px-2 py-1" style="font-size: 10px; border-radius: 4px;">
                            {{ $item->apPhoto->category->name ?? 'Uncategorized' }}
                        </span>
                        <span class="text-muted small"><i class="far fa-image mr-1"></i></span>
                    </div>
                    @if($item->apPhoto->tags->count() > 0)
                        <div class="mt-2">
                            @foreach($item->apPhoto->tags->take(3) as $tag)
                                <span class="badge badge-light text-muted mr-1" style="font-size: 10px; font-weight: 400;">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </a>
        </div>
    </div>
@endforeach
