@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Media Library') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.All Media') }}</h4>
                <div class="card-header-action">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadMediaModal">
                        <i class="fas fa-upload"></i> {{ __('admin.Upload Media') }}
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('admin.Search') }}</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="{{ __('admin.Search media...') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('admin.File Type') }}</label>
                            <select id="typeFilter" class="form-control">
                                <option value="">{{ __('admin.All Types') }}</option>
                                <option value="image">{{ __('admin.Images') }}</option>
                                <option value="video">{{ __('admin.Videos') }}</option>
                                <option value="document">{{ __('admin.Documents') }}</option>
                                <option value="audio">{{ __('admin.Audio') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('admin.Uploaded By') }}</label>
                            <select id="uploaderFilter" class="form-control">
                                <option value="">{{ __('admin.All Users') }}</option>
                                @foreach($uploaders as $uploader)
                                    <option value="{{ $uploader->id }}">{{ $uploader->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" id="clearFilters" class="btn btn-secondary btn-block">
                                <i class="fas fa-times"></i> {{ __('admin.Clear') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- View Toggle -->
                <div class="mb-3 text-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary view-toggle active" data-view="grid">
                            <i class="fas fa-th"></i> {{ __('admin.Grid') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-primary view-toggle" data-view="list">
                            <i class="fas fa-list"></i> {{ __('admin.List') }}
                        </button>
                    </div>
                </div>

                <!-- Media Grid -->
                <div id="mediaContainer">
                    <div class="row" id="mediaGrid">
                        @forelse($media as $item)
                            <div class="col-md-3 col-sm-4 col-6 mb-4 media-item" data-type="{{ $item->file_type }}">
                                <div class="card media-card">
                                    <div class="media-thumbnail">
                                        @if($item->file_type === 'image')
                                            <img src="{{ $item->file_url }}" alt="{{ $item->alt_text ?? $item->title }}" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                        @elseif($item->file_type === 'video')
                                            <div class="d-flex align-items-center justify-content-center" style="height: 200px; background: #f0f0f0;">
                                                <i class="fas fa-video fa-3x text-primary"></i>
                                            </div>
                                        @elseif($item->file_type === 'audio')
                                            <div class="d-flex align-items-center justify-content-center" style="height: 200px; background: #f0f0f0;">
                                                <i class="fas fa-music fa-3x text-primary"></i>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center" style="height: 200px; background: #f0f0f0;">
                                                <i class="fas fa-file fa-3x text-primary"></i>
                                            </div>
                                        @endif
                                        <div class="media-overlay">
                                            <div class="media-actions">
                                                <button type="button" class="btn btn-sm btn-info view-media" data-id="{{ $item->id }}" title="{{ __('admin.View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning edit-media" data-id="{{ $item->id }}" title="{{ __('admin.Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-media" data-id="{{ $item->id }}" title="{{ __('admin.Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <h6 class="mb-1 text-truncate" title="{{ $item->title ?? $item->original_filename }}">
                                            {{ $item->title ?? $item->original_filename }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-{{ $item->file_type === 'image' ? 'image' : ($item->file_type === 'video' ? 'video' : 'file') }}"></i>
                                            {{ strtoupper($item->file_type) }} • {{ $item->human_readable_size }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i> {{ __('admin.No media found') }}
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $media->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Upload Media Modal -->
    @include('admin.media-library.partials.upload-modal')

    <!-- Edit Media Modal -->
    @include('admin.media-library.partials.edit-modal')

    <!-- View Media Modal -->
    @include('admin.media-library.partials.view-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // View toggle
    $('.view-toggle').on('click', function() {
        $('.view-toggle').removeClass('active');
        $(this).addClass('active');
        const view = $(this).data('view');
        // Implement list view if needed
    });

    // Filter functionality
    $('#searchInput, #typeFilter, #uploaderFilter').on('change keyup', function() {
        applyFilters();
    });

    $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#typeFilter').val('');
        $('#uploaderFilter').val('');
        applyFilters();
    });

    function applyFilters() {
        const search = $('#searchInput').val().toLowerCase();
        const type = $('#typeFilter').val();
        const uploader = $('#uploaderFilter').val();

        $('.media-item').each(function() {
            const $item = $(this);
            const itemType = $item.data('type');
            const itemText = $item.text().toLowerCase();

            let show = true;

            if (search && !itemText.includes(search)) {
                show = false;
            }
            if (type && itemType !== type) {
                show = false;
            }

            $item.toggle(show);
        });
    }

    // Delete media
    $(document).on('click', '.delete-media', function() {
        const id = $(this).data('id');
        const $item = $(this).closest('.media-item');

        Swal.fire({
            title: '{{ __('admin.Are you sure?') }}',
            text: '{{ __('admin.This action cannot be undone') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __('admin.Yes, delete it') }}',
            cancelButtonText: '{{ __('admin.Cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/media-library/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $item.fadeOut(300, function() {
                            $(this).remove();
                            if ($('.media-item:visible').length === 0) {
                                $('#mediaGrid').html('<div class="col-12"><div class="alert alert-info text-center"><i class="fas fa-info-circle"></i> {{ __('admin.No media found') }}</div></div>');
                            }
                        });
                        Swal.fire('{{ __('admin.Deleted') }}', '{{ __('admin.Media deleted successfully') }}', 'success');
                    },
                    error: function() {
                        Swal.fire('{{ __('admin.Error') }}', '{{ __('admin.Failed to delete media') }}', 'error');
                    }
                });
            }
        });
    });

    // View media
    $(document).on('click', '.view-media', function() {
        const id = $(this).data('id');
        // Load media details and show modal
        $.ajax({
            url: '/admin/media-library/' + id,
            method: 'GET',
            success: function(response) {
                // Populate view modal
                $('#viewMediaModal').modal('show');
                // Add view modal content population here
            }
        });
    });

    // Edit media
    $(document).on('click', '.edit-media', function() {
        const id = $(this).data('id');
        // Load media details and show edit modal
        $.ajax({
            url: '/admin/media-library/' + id,
            method: 'GET',
            success: function(response) {
                $('#editMediaId').val(response.id);
                $('#editTitle').val(response.title || '');
                $('#editAltText').val(response.alt_text || '');
                $('#editCaption').val(response.caption || '');
                $('#editDescription').val(response.description || '');
                $('#editIsFeatured').prop('checked', response.is_featured);
                $('#editMediaModal').modal('show');
            }
        });
    });
});
</script>
<style>
.media-card {
    position: relative;
    transition: transform 0.2s;
    cursor: pointer;
}

.media-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.media-thumbnail {
    position: relative;
    overflow: hidden;
}

.media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.media-card:hover .media-overlay {
    opacity: 1;
}

.media-actions {
    display: flex;
    gap: 10px;
}
</style>
@endpush

