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
                            <label>{{ __('Search') }}</label>
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
    
    <!-- Image Cropper Modal (shared from media-modal) -->
    @include('admin.media-library.partials.cropper-modal')
@endsection

@push('scripts')
<script>
(function waitForjQuery(callback) {
    if (window.jQuery) {
        callback(window.jQuery);
    } else {
        setTimeout(function() {
            waitForjQuery(callback);
        }, 100);
    }
})(function($) {
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
            title: '{{ __('Are you sure?') }}',
            html: '<p>{{ __('This action cannot be undone') }}</p><p class="text-danger mt-2"><i class="fas fa-exclamation-triangle"></i> <strong>{{ __('Warning') }}:</strong> {{ __('Deleting this media will break all links where it is used (news articles, pages, etc.)') }}</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __('Yes, delete it') }}',
            cancelButtonText: '{{ __('Cancel') }}'
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
        if (!id) {
            Swal.fire('{{ __('Error') }}', '{{ __('Invalid media ID') }}', 'error');
            return;
        }
        
        // Show loading state
        $('#viewMediaContent').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">{{ __('Loading...') }}</p></div>');
        $('#viewMediaModal').modal('show');
        
        // Load media details and show modal
        $.ajax({
            url: '/admin/media-library/' + id,
            method: 'GET',
            success: function(response) {
                if (!response) {
                    Swal.fire('{{ __('Error') }}', '{{ __('Failed to load media details') }}', 'error');
                    $('#viewMediaModal').modal('hide');
                    return;
                }
                let html = '';
                
                // Media Preview Section
                html += '<div class="text-center mb-4">';
                if (response.file_type === 'image') {
                    html += '<img src="' + response.file_url + '" alt="' + (response.alt_text || response.title || 'Media') + '" class="img-fluid" style="max-height: 400px; border: 1px solid #ddd; border-radius: 4px; padding: 10px; background: #f8f9fa;">';
                } else if (response.file_type === 'video') {
                    html += '<div class="d-flex align-items-center justify-content-center" style="height: 300px; background: #f0f0f0; border-radius: 4px;">';
                    html += '<i class="fas fa-video fa-5x text-primary"></i>';
                    html += '</div>';
                } else if (response.file_type === 'audio') {
                    html += '<div class="d-flex align-items-center justify-content-center" style="height: 300px; background: #f0f0f0; border-radius: 4px;">';
                    html += '<i class="fas fa-music fa-5x text-primary"></i>';
                    html += '</div>';
                } else {
                    html += '<div class="d-flex align-items-center justify-content-center" style="height: 300px; background: #f0f0f0; border-radius: 4px;">';
                    html += '<i class="fas fa-file fa-5x text-primary"></i>';
                    html += '</div>';
                }
                html += '</div>';
                
                // Media Details
                html += '<div class="row">';
                html += '<div class="col-md-6">';
                html += '<table class="table table-bordered">';
                html += '<tbody>';
                
                // Title
                html += '<tr>';
                html += '<th style="width: 40%;">{{ __('Title') }}</th>';
                html += '<td>' + (response.title ? escapeHtml(response.title) : '<span class="text-muted">-</span>') + '</td>';
                html += '</tr>';
                
                // Original Filename
                html += '<tr>';
                html += '<th>{{ __('Filename') }}</th>';
                html += '<td><code>' + (response.original_filename ? escapeHtml(response.original_filename) : '-') + '</code></td>';
                html += '</tr>';
                
                // File Type
                html += '<tr>';
                html += '<th>{{ __('File Type') }}</th>';
                html += '<td><span class="badge badge-primary">' + (response.file_type ? response.file_type.toUpperCase() : '-') + '</span></td>';
                html += '</tr>';
                
                // MIME Type
                html += '<tr>';
                html += '<th>{{ __('MIME Type') }}</th>';
                html += '<td><code>' + (response.mime_type || '-') + '</code></td>';
                html += '</tr>';
                
                // File Size
                html += '<tr>';
                html += '<th>{{ __('File Size') }}</th>';
                html += '<td>' + (response.human_readable_size || formatFileSize(response.file_size) || '-') + '</td>';
                html += '</tr>';
                
                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                
                html += '<div class="col-md-6">';
                html += '<table class="table table-bordered">';
                html += '<tbody>';
                
                // Dimensions (for images/videos)
                if (response.width && response.height) {
                    html += '<tr>';
                    html += '<th style="width: 40%;">{{ __('Dimensions') }}</th>';
                    html += '<td>' + response.width + ' × ' + response.height + ' px</td>';
                    html += '</tr>';
                }
                
                // Alt Text
                html += '<tr>';
                html += '<th>{{ __('Alt Text') }}</th>';
                html += '<td>' + (response.alt_text ? escapeHtml(response.alt_text) : '<span class="text-muted">-</span>') + '</td>';
                html += '</tr>';
                
                // Uploaded By
                html += '<tr>';
                html += '<th>{{ __('Uploaded By') }}</th>';
                html += '<td>' + (response.uploader ? escapeHtml(response.uploader.name) : '-') + '</td>';
                html += '</tr>';
                
                // Uploaded Date
                html += '<tr>';
                html += '<th>{{ __('Uploaded Date') }}</th>';
                html += '<td>' + (response.created_at ? new Date(response.created_at).toLocaleString() : '-') + '</td>';
                html += '</tr>';
                
                // Last Updated
                html += '<tr>';
                html += '<th>{{ __('Last Updated') }}</th>';
                html += '<td>' + (response.updated_at ? new Date(response.updated_at).toLocaleString() : '-') + '</td>';
                html += '</tr>';
                
                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                html += '</div>';
                
                // Caption
                if (response.caption) {
                    html += '<div class="form-group mt-3">';
                    html += '<label><strong>{{ __('Caption') }}</strong></label>';
                    html += '<p class="text-muted">' + escapeHtml(response.caption) + '</p>';
                    html += '</div>';
                }
                
                // Description
                if (response.description) {
                    html += '<div class="form-group mt-3">';
                    html += '<label><strong>{{ __('Description') }}</strong></label>';
                    html += '<p class="text-muted">' + escapeHtml(response.description) + '</p>';
                    html += '</div>';
                }
                
                // File URL
                html += '<div class="form-group mt-3">';
                html += '<label><strong>{{ __('File URL') }}</strong></label>';
                html += '<div class="input-group">';
                html += '<input type="text" class="form-control" value="' + (response.file_url || '') + '" readonly id="viewMediaUrl">';
                html += '<div class="input-group-append">';
                html += '<button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard(\'#viewMediaUrl\')"><i class="fas fa-copy"></i> {{ __('Copy') }}</button>';
                html += '<a href="' + (response.file_url || '#') + '" target="_blank" class="btn btn-outline-primary"><i class="fas fa-external-link-alt"></i> {{ __('Open') }}</a>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                
                // Populate modal content
                $('#viewMediaContent').html(html);
                $('#viewMediaModal').modal('show');
            },
            error: function(xhr) {
                console.error('View media error:', xhr);
                let errorMessage = '{{ __('Failed to load media details') }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMessage = '{{ __('Media not found') }}';
                } else if (xhr.status === 500) {
                    errorMessage = '{{ __('Server error occurred') }}';
                }
                Swal.fire('{{ __('Error') }}', errorMessage, 'error');
                $('#viewMediaModal').modal('hide');
            }
        });
    });
    
    // Helper function to format file size
    function formatFileSize(bytes) {
        if (!bytes) return '-';
        const units = ['B', 'KB', 'MB', 'GB', 'TB'];
        let size = bytes;
        let unitIndex = 0;
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        return Math.round(size * 100) / 100 + ' ' + units[unitIndex];
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Helper function to copy to clipboard
    function copyToClipboard(selector) {
        const input = document.querySelector(selector);
        input.select();
        document.execCommand('copy');
        Swal.fire({
            icon: 'success',
            title: '{{ __('Copied') }}',
            text: '{{ __('URL copied to clipboard') }}',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Edit media
    $(document).on('click', '.edit-media', function() {
        const id = $(this).data('id');
        if (!id) {
            Swal.fire('{{ __('Error') }}', '{{ __('Invalid media ID') }}', 'error');
            return;
        }
        
        // Load media details and show edit modal
        $.ajax({
            url: '/admin/media-library/' + id,
            method: 'GET',
            success: function(response) {
                if (!response) {
                    Swal.fire('{{ __('Error') }}', '{{ __('Failed to load media details') }}', 'error');
                    return;
                }
                $('#editMediaId').val(response.id);
                $('#editMediaType').val(response.file_type || '');
                $('#editTitle').val(response.title || '');
                $('#editAltText').val(response.alt_text || '');
                $('#editCaption').val(response.caption || '');
                $('#editDescription').val(response.description || '');
                
                // Show image preview if it's an image
                const $preview = $('#editMediaPreview');
                const $previewImage = $('#editPreviewImage');
                
                if (response.file_type === 'image' && response.file_url) {
                    $previewImage.attr('src', response.file_url);
                    $previewImage.attr('alt', response.alt_text || response.title || 'Media Preview');
                    $preview.show();
                } else {
                    $preview.hide();
                }
                
                $('#editMediaModal').modal('show');
            },
            error: function(xhr) {
                console.error('Edit media error:', xhr);
                let errorMessage = '{{ __('Failed to load media details') }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMessage = '{{ __('Media not found') }}';
                } else if (xhr.status === 500) {
                    errorMessage = '{{ __('Server error occurred') }}';
                }
                Swal.fire('{{ __('Error') }}', errorMessage, 'error');
            }
        });
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

