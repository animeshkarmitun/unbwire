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
                                    <button type="button" class="btn btn-info btn-sm" id="openCropperBtn">
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
let currentFileForCropper = null;
let useCroppedImage = false;

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
                currentFileForCropper = file;
                useCroppedImage = false;
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
        
        const formData = new FormData(this);
        const $submitBtn = $('#uploadSubmitBtn');
        const originalText = $submitBtn.html();
        
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
        $('#quickUploadForm')[0].reset();
        $('#quickPreview').hide();
        $('#cropButtonContainer').hide();
        $('#imageOptionsGroup').hide();
        $('#watermarkPositionGroup').hide();
        $('#convertToWebp').prop('checked', true);
        $('#addWatermark').prop('checked', false);
        $('#keepOriginal').prop('checked', true);
        currentFileForCropper = null;
        useCroppedImage = false;
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

<!-- Image Cropper Modal -->
<div class="modal fade" id="imageCropperModal" tabindex="-1" role="dialog" aria-labelledby="imageCropperModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="z-index: 1060;">
    <div class="modal-dialog modal-xl" role="document" style="z-index: 1061;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageCropperModalLabel">{{ __('Crop Image') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="row">
                    <div class="col-md-9">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> <strong>{{ __('How to Crop:') }}</strong>
                            <ul class="mb-0 mt-2">
                                <li>{{ __('Click and drag the blue crop box to move it') }}</li>
                                <li>{{ __('Drag the blue corner handles to resize the crop area') }}</li>
                                <li>{{ __('Use aspect ratio buttons below to lock proportions') }}</li>
                                <li>{{ __('Double-click to toggle between move and crop mode') }}</li>
                            </ul>
                        </div>
                        <div class="cropper-wrapper" style="background: #f5f5f5; border: 2px solid #ddd; border-radius: 4px; padding: 10px; min-height: 500px; max-height: 70vh; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            <div class="img-container" id="cropperContainer" style="width: 100%; max-width: 100%; position: relative; max-height: 70vh;">
                                <img id="cropperImage" src="" alt="Crop me" style="display: block; max-width: 100%; max-height: 70vh;">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="font-weight-bold mb-2 d-block">{{ __('Aspect Ratio:') }}</label>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary active" id="aspectRatioFree">{{ __('Free') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="aspectRatio1_1">1:1</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="aspectRatio16_9">16:9</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="aspectRatio4_3">4:3</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="aspectRatio3_2">3:2</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Crop Actions') }}</label>
                            <button type="button" class="btn btn-secondary btn-sm btn-block" id="resetCrop">
                                <i class="fas fa-undo"></i> {{ __('Reset') }}
                            </button>
                            <button type="button" class="btn btn-primary btn-sm btn-block mt-2" id="cropImage">
                                <i class="fas fa-check"></i> {{ __('Crop & Continue') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block mt-2" data-dismiss="modal">
                                <i class="fas fa-times"></i> {{ __('Cancel') }}
                            </button>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Crop Info') }}</label>
                            <div class="small text-muted">
                                <div>Width: <span id="cropWidth">-</span>px</div>
                                <div>Height: <span id="cropHeight">-</span>px</div>
                                <div>X: <span id="cropX">-</span>px</div>
                                <div>Y: <span id="cropY">-</span>px</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    #imageCropperModal {
        z-index: 1060 !important;
    }
    #imageCropperModal .modal-dialog {
        z-index: 1061 !important;
        max-width: 95%;
    }
    .cropper-wrapper {
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        min-height: 500px;
        max-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .img-container {
        width: 100%;
        max-width: 100%;
        position: relative;
        max-height: 70vh;
    }
    .cropper-container {
        direction: ltr;
        max-width: 100%;
        max-height: 70vh;
    }
    .cropper-container img {
        max-width: 100%;
        max-height: 70vh;
        display: block;
        opacity: 1 !important;
    }
    .cropper-canvas {
        max-width: 100% !important;
        max-height: 70vh !important;
    }
    /* Make crop box and handles more visible - WordPress style */
    .cropper-view-box {
        outline: 3px solid #0073aa !important;
        outline-color: rgba(0, 115, 170, 0.9) !important;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5) !important;
    }
    .cropper-face {
        background-color: rgba(0, 115, 170, 0.1) !important;
        cursor: move !important;
    }
    .cropper-line {
        background-color: #0073aa !important;
        opacity: 0.6 !important;
    }
    .cropper-point {
        background-color: #0073aa !important;
        width: 12px !important;
        height: 12px !important;
        opacity: 1 !important;
        border: 2px solid #fff !important;
        box-shadow: 0 0 4px rgba(0,0,0,0.6) !important;
        cursor: pointer !important;
    }
    .cropper-point:hover {
        background-color: #005177 !important;
        transform: scale(1.3);
        box-shadow: 0 0 6px rgba(0,0,0,0.8) !important;
    }
    /* Corner points */
    .cropper-point.point-se,
    .cropper-point.point-sw,
    .cropper-point.point-nw,
    .cropper-point.point-ne {
        width: 14px !important;
        height: 14px !important;
    }
    /* Edge points */
    .cropper-point.point-n,
    .cropper-point.point-s {
        cursor: ns-resize !important;
    }
    .cropper-point.point-e,
    .cropper-point.point-w {
        cursor: ew-resize !important;
    }
    /* Make sure cropper is visible */
    .cropper-bg {
        background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjZGRkIi8+PHJlY3QgeD0iMTAiIHk9IjEwIiB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIGZpbGw9IiNkZGQiLz48L3N2Zz4=') !important;
    }
    /* Ensure cropper handles are visible */
    .cropper-view-box,
    .cropper-face {
        border-color: #39f !important;
    }
    .cropper-line {
        background-color: #39f !important;
    }
    .cropper-point {
        background-color: #39f !important;
        width: 8px !important;
        height: 8px !important;
    }
    .cropper-point.point-se {
        cursor: se-resize !important;
    }
    .cropper-point.point-sw {
        cursor: sw-resize !important;
    }
    .cropper-point.point-nw {
        cursor: nw-resize !important;
    }
    .cropper-point.point-ne {
        cursor: ne-resize !important;
    }
    .cropper-point.point-n,
    .cropper-point.point-s {
        cursor: ns-resize !important;
    }
    .cropper-point.point-e,
    .cropper-point.point-w {
        cursor: ew-resize !important;
    }
    /* Ensure cropper modal appears above media library modal */
    .modal-backdrop.show {
        z-index: 1055;
    }
    #imageCropperModal.show {
        z-index: 1060;
    }
    .btn-group .btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    let cropper;
    let cropperLibraryLoaded = false;
    
    // Check if Cropper library is loaded
    function checkCropperLibrary() {
        if (typeof Cropper !== 'undefined') {
            cropperLibraryLoaded = true;
            return true;
        }
        return false;
    }
    
    // Wait for Cropper library to load
    if (typeof Cropper === 'undefined') {
        // Try to load it or wait for it
        const checkInterval = setInterval(function() {
            if (checkCropperLibrary()) {
                clearInterval(checkInterval);
            }
        }, 100);
        
        // Timeout after 5 seconds
        setTimeout(function() {
            clearInterval(checkInterval);
            if (!cropperLibraryLoaded) {
                console.error('Cropper library failed to load');
            }
        }, 5000);
    } else {
        cropperLibraryLoaded = true;
    }

    $(document).ready(function() {
        // Show watermark position when watermark is enabled
        $('#addWatermark').on('change', function() {
            $('#watermarkPositionGroup').toggle(this.checked);
        });


        // Open cropper modal
        $('#openCropperBtn').on('click', function() {
            if (!currentFileForCropper) {
                alert('{{ __('Please select an image first') }}');
                return;
            }
            
            // Check if Cropper library is loaded
            if (!checkCropperLibrary()) {
                alert('{{ __('Image cropper library is loading. Please wait a moment and try again.') }}');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageSrc = e.target.result;
                const imageElement = document.getElementById('cropperImage');
                
                // Reset image first
                imageElement.src = '';
                
                // Show modal first
                $('#imageCropperModal').modal('show');
                
                // Set image source after modal is shown
                setTimeout(function() {
                    // Ensure image is visible
                    $(imageElement).css({
                        'opacity': '1',
                        'display': 'block',
                        'visibility': 'visible'
                    });
                    
                    imageElement.src = imageSrc;
                    
                    // Wait for image to load, then initialize cropper
                    if (imageElement.complete && imageElement.naturalWidth > 0) {
                        // Image already loaded
                        setTimeout(function() {
                            initializeCropper();
                        }, 300);
                    } else {
                        // Wait for image to load
                        imageElement.onload = function() {
                            setTimeout(function() {
                                initializeCropper();
                            }, 300);
                        };
                        imageElement.onerror = function() {
                            alert('{{ __('Failed to load image') }}');
                        };
                    }
                }, 300);
            };
            reader.onerror = function() {
                alert('{{ __('Failed to read image file') }}');
            };
            reader.readAsDataURL(currentFileForCropper);
        });

        // Initialize cropper function
        function initializeCropper() {
            const image = document.getElementById('cropperImage');
            
            if (!image) {
                console.error('Cropper image element not found');
                return;
            }
            
            // Wait for image to load completely
            if (image.complete && image.naturalWidth > 0) {
                // Image already loaded
                setTimeout(function() {
                    createCropper(image);
                }, 100);
            } else {
                // Wait for image to load
                image.onload = function() {
                    setTimeout(function() {
                        createCropper(image);
                    }, 100);
                };
                image.onerror = function() {
                    alert('{{ __('Failed to load image') }}');
                };
            }
        }

        function createCropper(imageElement) {
            // Destroy existing cropper if any
            if (cropper) {
                try {
                    cropper.destroy();
                } catch (e) {
                    console.warn('Error destroying cropper:', e);
                }
                cropper = null;
            }
            
            // Check if Cropper is available
            if (typeof Cropper === 'undefined') {
                alert('{{ __('Cropper library not loaded. Please refresh the page.') }}');
                return;
            }
            
            // Ensure image is visible
            if (!imageElement || !imageElement.src) {
                alert('{{ __('Image not loaded. Please try again.') }}');
                return;
            }
            
            try {
                // Ensure image is visible and has dimensions
                if (imageElement.naturalWidth === 0 || imageElement.naturalHeight === 0) {
                    console.error('Image has no dimensions');
                    alert('{{ __('Image failed to load. Please try again.') }}');
                    return;
                }
                
                // Make sure image is visible
                $(imageElement).css({
                    'opacity': '1',
                    'display': 'block',
                    'max-width': '100%',
                    'max-height': '70vh'
                });
                
                console.log('Creating cropper with image dimensions:', imageElement.naturalWidth, 'x', imageElement.naturalHeight);
                
                // Create new cropper instance with WordPress-like settings
                cropper = new Cropper(imageElement, {
                    aspectRatio: NaN, // Free aspect ratio by default
                    viewMode: 1, // Restrict crop box within canvas
                    dragMode: 'move', // Default drag mode - can move crop box
                    autoCropArea: 0.8, // 80% of image
                    restore: false,
                    guides: true, // Show grid guides (3x3 grid)
                    center: true, // Center crop box
                    highlight: true, // Highlight crop box
                    cropBoxMovable: true, // Allow moving crop box by dragging
                    cropBoxResizable: true, // Allow resizing crop box by dragging handles
                    toggleDragModeOnDblclick: true, // Toggle between move and crop on double click
                    minCanvasWidth: 0,
                    minCanvasHeight: 0,
                    minCropBoxWidth: 10,
                    minCropBoxHeight: 10,
                    responsive: true,
                    checkOrientation: true,
                    modal: true, // Show dark background outside crop box (WordPress style)
                    background: true, // Show background
                    zoomable: true, // Allow zooming with mouse wheel
                    scalable: true, // Allow scaling
                    rotatable: false, // Disable rotation for simplicity
                    movable: true, // Allow moving the image
                    ready: function() {
                        console.log('Cropper initialized successfully');
                        console.log('Image dimensions:', imageElement.naturalWidth, 'x', imageElement.naturalHeight);
                        console.log('Cropper canvas:', cropper.getCanvasData());
                        
                        // Ensure crop box is visible and movable
                        const cropBox = cropper.getCropBoxData();
                        console.log('Initial crop box:', cropBox);
                        
                        // Verify cropper is working
                        if (cropper && cropper.cropped) {
                            console.log('Cropper is active and ready');
                        }
                        
                        updateCropInfo();
                        
                        // Show visual indicator that cropping is ready
                        setTimeout(function() {
                            console.log('Cropper ready - you can now drag the crop box');
                            // Flash the crop box to show it's ready
                            const viewBox = document.querySelector('.cropper-view-box');
                            if (viewBox) {
                                viewBox.style.transition = 'outline 0.3s';
                                viewBox.style.outline = '3px solid #00ff00';
                                setTimeout(function() {
                                    viewBox.style.outline = '3px solid #0073aa';
                                }, 500);
                            }
                        }, 100);
                    },
                    crop: function(event) {
                        // Update crop info in real-time
                        updateCropInfo();
                    },
                    cropstart: function() {
                        console.log('Crop started');
                    },
                    cropmove: function() {
                        updateCropInfo();
                    },
                    cropend: function() {
                        console.log('Crop ended');
                        updateCropInfo();
                    }
                });
                
                console.log('Cropper instance created successfully');
            } catch (error) {
                console.error('Error creating cropper:', error);
                alert('{{ __('Failed to initialize image cropper. Please try again.') }}: ' + error.message);
            }
        }
        
        // Update crop information display
        function updateCropInfo() {
            if (!cropper) return;
            
            const cropData = cropper.getData();
            $('#cropWidth').text(Math.round(cropData.width));
            $('#cropHeight').text(Math.round(cropData.height));
            $('#cropX').text(Math.round(cropData.x));
            $('#cropY').text(Math.round(cropData.y));
        }
        
        // Aspect ratio handlers
        $('#aspectRatioFree').on('click', function() {
            if (cropper) {
                cropper.setAspectRatio(NaN);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
        
        $('#aspectRatio1_1').on('click', function() {
            if (cropper) {
                cropper.setAspectRatio(1);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
        
        $('#aspectRatio16_9').on('click', function() {
            if (cropper) {
                cropper.setAspectRatio(16 / 9);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
        
        $('#aspectRatio4_3').on('click', function() {
            if (cropper) {
                cropper.setAspectRatio(4 / 3);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
        
        $('#aspectRatio3_2').on('click', function() {
            if (cropper) {
                cropper.setAspectRatio(3 / 2);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });

        // Initialize cropper when modal is shown (backup)
        $('#imageCropperModal').on('shown.bs.modal', function() {
            // Give it a moment for the image to be visible
            setTimeout(function() {
                const image = document.getElementById('cropperImage');
                if (image && image.src && !cropper) {
                    initializeCropper();
                }
            }, 200);
        });
        
        // Set default aspect ratio to free
        $('#aspectRatioFree').addClass('active');

        // Destroy cropper when modal is hidden
        $('#imageCropperModal').on('hidden.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Reset crop
        $('#resetCrop').on('click', function() {
            if (cropper) {
                cropper.reset();
            }
        });

        // Crop and continue
        $('#cropImage').on('click', function() {
            if (!cropper) {
                alert('{{ __('Cropper not initialized. Please wait a moment and try again.') }}');
                return;
            }

            try {
                const canvas = cropper.getCroppedCanvas({
                    width: cropper.getCroppedCanvas().width,
                    height: cropper.getCroppedCanvas().height,
                });

                if (!canvas) {
                    alert('{{ __('Could not get cropped canvas') }}');
                    return;
                }

                // Convert canvas to blob and update preview
                canvas.toBlob(function(blob) {
                    if (!blob) {
                        alert('{{ __('Failed to create cropped image') }}');
                        return;
                    }
                    
                    const file = new File([blob], currentFileForCropper.name, { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    $('#quickUploadFile')[0].files = dataTransfer.files;
                    
                    // Update preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#quickPreviewImage').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                    
                    currentFileForCropper = file;
                    useCroppedImage = true;
                    $('#imageCropperModal').modal('hide');
                }, 'image/jpeg', 0.9);
            } catch (error) {
                console.error('Crop error:', error);
                alert('{{ __('An error occurred while cropping the image') }}');
            }
        });

    });
</script>
@endpush

