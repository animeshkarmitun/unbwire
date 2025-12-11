<!-- Media Library Modal for Summernote Editor -->
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" role="dialog" aria-labelledby="mediaLibraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaLibraryModalLabel">{{ __('Media Library') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="mediaTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="upload-tab" data-toggle="tab" href="#uploadTab" role="tab">
                            <i class="fas fa-upload"></i> {{ __('Upload') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="library-tab" data-toggle="tab" href="#libraryTab" role="tab">
                            <i class="fas fa-images"></i> {{ __('Media Library') }}
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="mediaTabsContent">
                    <!-- Upload Tab -->
                    <div class="tab-pane fade show active" id="uploadTab" role="tabpanel">
                        <form id="quickUploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('Select File') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="quickUploadFile" name="file" required>
                                    <label class="custom-file-label" for="quickUploadFile">{{ __('Choose file') }}</label>
                                </div>
                            </div>

                            <div id="quickPreview" class="mb-3" style="display: none;">
                                <img id="quickPreviewImage" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                <div class="mt-2" id="cropButtonContainer" style="display: none;">
                                    <button type="button" class="btn btn-info btn-sm" id="openCropperBtnUpload">
                                        <i class="fas fa-crop"></i> {{ __('Crop Image') }}
                                    </button>
                                </div>
                            </div>

                            <div class="form-group" id="imageOptionsGroup" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="convertToWebp" checked>
                                            <label class="form-check-label" for="convertToWebp">
                                                {{ __('Convert to WebP') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="addWatermark">
                                            <label class="form-check-label" for="addWatermark">
                                                {{ __('Add Watermark') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="keepOriginal" checked>
                                            <label class="form-check-label" for="keepOriginal">
                                                {{ __('Keep Original File') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" id="watermarkPositionGroup" style="display: none;">
                                            <label>{{ __('Watermark Position') }}</label>
                                            <select class="form-control form-control-sm" id="watermarkPosition">
                                                <option value="center">{{ __('Center') }}</option>
                                                <option value="top-left">{{ __('Top Left') }}</option>
                                                <option value="top-center">{{ __('Top Center') }}</option>
                                                <option value="top-right">{{ __('Top Right') }}</option>
                                                <option value="middle-left">{{ __('Middle Left') }}</option>
                                                <option value="middle-right">{{ __('Middle Right') }}</option>
                                                <option value="bottom-left">{{ __('Bottom Left') }}</option>
                                                <option value="bottom-center">{{ __('Bottom Center') }}</option>
                                                <option value="bottom-right">{{ __('Bottom Right') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Title') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <input type="text" class="form-control" name="title" id="quickTitle" placeholder="{{ __('Enter title') }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Alt Text') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <input type="text" class="form-control" name="alt_text" id="quickAltText" placeholder="{{ __('Enter alt text') }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Caption') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <textarea class="form-control" name="caption" id="quickCaption" rows="2" placeholder="{{ __('Enter caption') }}"></textarea>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Description') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <textarea class="form-control" name="description" id="quickDescription" rows="3" placeholder="{{ __('Enter description') }}"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" id="uploadSubmitBtn">
                                <i class="fas fa-upload"></i> {{ __('Upload & Insert') }}
                            </button>
                        </form>
                    </div>

                    <!-- Library Tab -->
                    <div class="tab-pane fade" id="libraryTab" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" id="mediaSearch" class="form-control" placeholder="{{ __('Search media...') }}">
                            </div>
                            <div class="col-md-4">
                                <select id="mediaTypeFilter" class="form-control">
                                    <option value="image">{{ __('Images') }}</option>
                                    <option value="all">{{ __('All Types') }}</option>
                                    <option value="video">{{ __('Videos') }}</option>
                                    <option value="document">{{ __('Documents') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" id="loadMedia" class="btn btn-primary btn-block">
                                    <i class="fas fa-sync"></i> {{ __('Refresh') }}
                                </button>
                            </div>
                        </div>

                        <div id="mediaLibraryGrid" class="row">
                            <!-- Media items will be loaded here -->
                        </div>

                        <div id="mediaPagination" class="mt-3">
                            <!-- Pagination will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentEditor = null;
let currentPage = 1;
let selectionMode = 'editor'; // 'editor' or 'featured'
// Make these global so they're accessible across script blocks - use Upload version to match shared cropper modal
window.currentFileForCropperUpload = null;
window.useCroppedImageUpload = false;

(function waitForjQuery(callback) {
    if (window.jQuery) {
        callback(window.jQuery);
    } else {
        setTimeout(function() {
            waitForjQuery(callback);
        }, 50);
    }
})(function($) {
$(document).ready(function() {
    // Quick upload preview with cropping support
    $('#quickUploadFile').on('change', function() {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#quickPreviewImage').attr('src', e.target.result);
                $('#quickPreview').show();
                $('#cropButtonContainer').show();
                $('#imageOptionsGroup').show();
                window.currentFileForCropperUpload = file;
                window.useCroppedImageUpload = false;
                console.log('File selected for cropping:', file.name);
            };
            reader.readAsDataURL(file);
        } else {
            $('#quickPreview').hide();
            $('#cropButtonContainer').hide();
            $('#imageOptionsGroup').hide();
        }
    });

    // Quick upload form with cropping support
    $('#quickUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        // Check if we have a file to upload
        if (!window.currentFileForCropperUpload) {
            alert('{{ __('Please select an image first') }}');
            return;
        }
        
        console.log('Quick upload form submitted');
        console.log('Using cropped image:', window.useCroppedImageUpload);
        console.log('File:', window.currentFileForCropperUpload);
        
        const formData = new FormData(this);
        const $submitBtn = $('#uploadSubmitBtn');
        const originalText = $submitBtn.html();
        
        // Use cropped file if available
        if (window.useCroppedImageUpload && window.currentFileForCropperUpload) {
            formData.delete('file');
            formData.append('file', window.currentFileForCropperUpload);
            console.log('Using cropped image for upload');
        }
        
        // Add processing options
        formData.append('convert_to_webp', $('#convertToWebp').is(':checked') ? 1 : 0);
        formData.append('add_watermark', $('#addWatermark').is(':checked') ? 1 : 0);
        formData.append('watermark_position', $('#watermarkPosition').val());
        
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('Uploading') }}...');

        $.ajax({
            url: '{{ route('admin.upload-image') }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.url || (response.success && response.media)) {
                    // Create a media-like object for compatibility
                    let media;
                    if (response.success && response.media) {
                        // Media library format response
                        media = {
                            file_url: response.media.file_url,
                            file_path: response.media.file_path,
                            title: response.media.title || '',
                            alt_text: response.media.alt_text || '',
                            caption: response.media.caption || '',
                            description: response.media.description || '',
                        };
                    } else {
                        // Editor format response (backward compatibility)
                        media = {
                            file_url: response.url,
                            file_path: response.url.replace('{{ asset("") }}', ''),
                            title: $('#quickTitle').val() || '',
                            alt_text: $('#quickAltText').val() || '',
                            caption: $('#quickCaption').val() || '',
                            description: $('#quickDescription').val() || '',
                        };
                    }
                    
                    if (selectionMode === 'featured') {
                        setFeaturedImage(media);
                    } else {
                        insertMediaToEditor(media);
                    }
                    $('#mediaLibraryModal').modal('hide');
                    resetQuickUploadForm();
                }
                $submitBtn.prop('disabled', false).html(originalText);
            },
            error: function(xhr) {
                let errorMessage = '{{ __('Error uploading media') }}';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                Swal.fire('{{ __('Error') }}', errorMessage, 'error');
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Load media library
    $('#loadMedia, #mediaTypeFilter').on('change click', function() {
        loadMediaLibrary();
    });

    $('#mediaSearch').on('keyup', debounce(function() {
        loadMediaLibrary();
    }, 500));

    function loadMediaLibrary(page = 1) {
        const search = $('#mediaSearch').val();
        const type = $('#mediaTypeFilter').val();
        currentPage = page;

        $.ajax({
            url: '{{ route('admin.media-library.api') }}',
            method: 'GET',
            data: {
                search: search,
                type: type,
                per_page: 20,
                page: page
            },
            success: function(response) {
                renderMediaGrid(response.data);
                renderPagination(response);
            },
            error: function() {
                $('#mediaLibraryGrid').html('<div class="col-12"><div class="alert alert-danger">{{ __('Error loading media') }}</div></div>');
            }
        });
    }

    function renderMediaGrid(media) {
        let html = '';
        if (media.length === 0) {
            html = '<div class="col-12"><div class="alert alert-info text-center">{{ __('No media found') }}</div></div>';
        } else {
            media.forEach(function(item) {
                const thumbnail = item.file_type === 'image' 
                    ? `<img src="${item.file_url}" alt="${item.alt_text || ''}" class="img-fluid" style="width: 100%; height: 150px; object-fit: cover;">`
                    : `<div class="d-flex align-items-center justify-content-center" style="height: 150px; background: #f0f0f0;"><i class="fas fa-${item.file_type === 'video' ? 'video' : 'file'} fa-2x text-primary"></i></div>`;
                
                html += `
                    <div class="col-md-3 col-sm-4 col-6 mb-3">
                        <div class="card media-select-card" data-id="${item.id}" style="cursor: pointer;">
                            <div class="media-select-thumbnail">${thumbnail}</div>
                            <div class="card-body p-2">
                                <small class="text-truncate d-block" title="${item.title || item.original_filename}">
                                    ${item.title || item.original_filename}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        $('#mediaLibraryGrid').html(html);

        // Add click handler for media selection
        $('.media-select-card').on('click', function() {
            const id = $(this).data('id');
            selectMedia(id);
        });
    }

    function renderPagination(response) {
        if (response.last_page <= 1) {
            $('#mediaPagination').html('');
            return;
        }

        let html = '<nav><ul class="pagination justify-content-center">';
        
        // Previous button
        html += `<li class="page-item ${response.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${response.current_page - 1}">Previous</a>
        </li>`;

        // Page numbers
        for (let i = 1; i <= response.last_page; i++) {
            if (i === 1 || i === response.last_page || (i >= response.current_page - 2 && i <= response.current_page + 2)) {
                html += `<li class="page-item ${i === response.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            } else if (i === response.current_page - 3 || i === response.current_page + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next button
        html += `<li class="page-item ${response.current_page === response.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${response.current_page + 1}">Next</a>
        </li>`;

        html += '</ul></nav>';
        $('#mediaPagination').html(html);

        $('.page-link[data-page]').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page > 0 && page <= response.last_page) {
                loadMediaLibrary(page);
            }
        });
    }

    function selectMedia(id) {
        $.ajax({
            url: '/admin/media-library/' + id,
            method: 'GET',
            success: function(response) {
                // Check if gallery selection mode is active
                if (typeof window.selectMediaForGallery === 'function') {
                    // Gallery selection mode
                    window.selectMediaForGallery(response);
                } else if (selectionMode === 'featured') {
                    // Set featured image
                    setFeaturedImage(response);
                } else {
                    // Insert into editor
                    insertMediaToEditor(response);
                }
                // Don't hide modal in gallery mode (let the handler decide)
                if (typeof window.selectMediaForGallery !== 'function') {
                    $('#mediaLibraryModal').modal('hide');
                }
            }
        });
    }
    
    function setFeaturedImage(media) {
        if (!media || !media.file_url) return;
        
        // Use file_path if available, otherwise use file_url
        var imagePath = media.file_path || media.file_url;
        
        // Set the hidden input value
        $('#featured-image-path').val(imagePath);
        
        // Update preview
        $('#featured-image-preview').css({
            'background-image': 'url(' + media.file_url + ')',
            'display': 'block'
        });
        
        // Hide select button, show change button
        $('#select-featured-image').hide();
        $('#change-featured-image').show();
    }

    function insertMediaToEditor(media) {
        if (!currentEditor) return;

        let imageHtml = '';
        const altText = media.alt_text || '';
        
        if (media.caption && media.caption.trim() !== '') {
            imageHtml = '<figure>' +
                '<img src="' + media.file_url + '" alt="' + escapeHtml(altText) + '" style="max-width: 100%; height: auto;">' +
                '<figcaption>' + escapeHtml(media.caption.trim()) + '</figcaption>' +
                '</figure>';
        } else {
            imageHtml = '<img src="' + media.file_url + '" alt="' + escapeHtml(altText) + '" style="max-width: 100%; height: auto;">';
        }

        $(currentEditor).summernote('pasteHTML', imageHtml);
    }

    function resetQuickUploadForm() {
        // Reset global variables
        window.currentFileForCropperUpload = null;
        window.useCroppedImageUpload = false;
        $('#quickUploadForm')[0].reset();
        $('#quickPreview').hide();
        $('#cropButtonContainer').hide();
        $('#imageOptionsGroup').hide();
        $('#watermarkPositionGroup').hide();
        $('#convertToWebp').prop('checked', true);
        $('#addWatermark').prop('checked', false);
        $('#keepOriginal').prop('checked', true);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Global function to open media library modal for editor
    window.openMediaLibrary = function(editor, mode) {
        // Make sure modal exists
        if ($('#mediaLibraryModal').length === 0) {
            console.error('Media library modal not found. Make sure it is included in the page.');
            // Fallback to old dialog if modal doesn't exist
            if (typeof showImageDialog === 'function') {
                showImageDialog(editor);
            }
            return false;
        }
        
        currentEditor = editor;
        selectionMode = mode || 'editor';
        
        // Show modal
        $('#mediaLibraryModal').modal('show');
        
        // Switch to library tab and load media after modal is shown
        $('#mediaLibraryModal').on('shown.bs.modal', function() {
            $('#library-tab').tab('show');
            loadMediaLibrary();
        });
        
        // If modal is already shown, load immediately
        if ($('#mediaLibraryModal').hasClass('show')) {
            $('#library-tab').tab('show');
            loadMediaLibrary();
        }
        
        return false; // Prevent any default behavior
    };
    
    // Global function to open media library modal for featured image
    window.openMediaLibraryForFeatured = function() {
        window.openMediaLibrary(null, 'featured');
    };
});
}); // end waitForjQuery
</script>

<style>
.media-select-card {
    transition: transform 0.2s;
}

.media-select-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.media-select-thumbnail {
    overflow: hidden;
}
</style>

<!-- Image Cropper Modal (shared) -->
@include('admin.media-library.partials.cropper-modal')

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
        // Show watermark position when watermark is enabled
        $('#addWatermark').on('change', function() {
            $('#watermarkPositionGroup').toggle(this.checked);
        });
        
        // The cropper functionality is now handled by cropper-modal.blade.php
        // which listens for clicks on #openCropperBtnUpload
        // We just need to ensure the file is set in window.currentFileForCropperUpload
    });
    });
</script>
@endpush

