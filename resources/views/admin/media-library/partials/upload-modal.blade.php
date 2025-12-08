<div class="modal fade" id="uploadMediaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.Upload Media') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadMediaForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('admin.Select File') }}</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="mediaFile" name="file" required>
                            <label class="custom-file-label" for="mediaFile">{{ __('admin.Choose file') }}</label>
                        </div>
                        <small class="form-text text-muted">{{ __('admin.Max file size: 10MB') }}</small>
                    </div>

                    <div id="filePreview" class="mb-3" style="display: none;">
                        <img id="previewImage" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                        <div class="mt-2" id="cropButtonContainer" style="display: none;">
                            <button type="button" class="btn btn-info btn-sm" id="openCropperBtnUpload">
                                <i class="fas fa-crop"></i> {{ __('Crop Image') }}
                            </button>
                        </div>
                    </div>

                    <div class="form-group" id="imageOptionsGroup" style="display: none;">
                        <label class="font-weight-bold">{{ __('Image Processing Options') }}</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="convertToWebpUpload" checked>
                                    <label class="form-check-label" for="convertToWebpUpload">
                                        {{ __('Convert to WebP') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="addWatermarkUpload">
                                    <label class="form-check-label" for="addWatermarkUpload">
                                        {{ __('Add Watermark') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="keepOriginalUpload" checked>
                                    <label class="form-check-label" for="keepOriginalUpload">
                                        {{ __('Keep Original File') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="watermarkPositionGroupUpload" style="display: none;">
                                    <label>{{ __('Watermark Position') }}</label>
                                    <select class="form-control form-control-sm" id="watermarkPositionUpload">
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
                        <label>{{ __('admin.Title') }} <small class="text-muted">({{ __('admin.Optional') }})</small></label>
                        <input type="text" class="form-control" name="title" id="uploadTitle" placeholder="{{ __('admin.Enter title') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Alt Text') }} <small class="text-muted">({{ __('admin.Optional') }})</small></label>
                        <input type="text" class="form-control" name="alt_text" id="uploadAltText" placeholder="{{ __('admin.Enter alt text') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Caption') }} <small class="text-muted">({{ __('admin.Optional') }})</small></label>
                        <textarea class="form-control" name="caption" id="uploadCaption" rows="2" placeholder="{{ __('admin.Enter caption') }}"></textarea>
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Description') }} <small class="text-muted">({{ __('admin.Optional') }})</small></label>
                        <textarea class="form-control" name="description" id="uploadDescription" rows="3" placeholder="{{ __('Enter description') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> {{ __('admin.Upload') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Aggressive z-index fix for upload modal - use very high values */
    #uploadMediaModal {
        z-index: 9999 !important;
    }
    #uploadMediaModal.show {
        z-index: 9999 !important;
    }
    #uploadMediaModal.modal {
        z-index: 9999 !important;
    }
    #uploadMediaModal .modal-dialog {
        z-index: 10000 !important;
        position: relative;
        pointer-events: auto !important;
    }
    #uploadMediaModal .modal-content {
        z-index: 10001 !important;
        position: relative;
        pointer-events: auto !important;
    }
    /* Ensure backdrop is below modal */
    .modal-backdrop {
        z-index: 1040 !important;
    }
    .modal-backdrop.show {
        z-index: 1040 !important;
    }
    /* Ensure all modal elements are clickable */
    #uploadMediaModal,
    #uploadMediaModal * {
        pointer-events: auto !important;
    }
    /* Fix for nested modals and body state */
    body.modal-open #uploadMediaModal {
        z-index: 9999 !important;
    }
    /* Ensure modal is visible and interactive */
    #uploadMediaModal.modal.show {
        display: block !important;
        opacity: 1 !important;
        z-index: 9999 !important;
    }
    #uploadMediaModal.modal.show .modal-dialog {
        pointer-events: auto !important;
        transform: none !important;
        z-index: 10000 !important;
    }
    #uploadMediaModal.modal.show .modal-content {
        z-index: 10001 !important;
        pointer-events: auto !important;
    }
    /* Override any sidebar or navigation z-index */
    body.modal-open .sidebar,
    body.modal-open .main-sidebar {
        z-index: 1030 !important;
    }
    /* Ensure modal dialog uses flexbox and doesn't overflow viewport */
    #uploadMediaModal .modal-dialog {
        max-height: 90vh !important;
        height: auto !important;
        margin: 1.75rem auto !important;
        display: flex !important;
        flex-direction: column !important;
    }
    /* Make modal content flex container */
    #uploadMediaModal .modal-content {
        display: flex !important;
        flex-direction: column !important;
        max-height: 90vh !important;
        height: auto !important;
        overflow: hidden !important;
    }
    /* Keep header fixed */
    #uploadMediaModal .modal-header {
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
    }
    /* Make modal body scrollable */
    #uploadMediaModal .modal-body {
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 1rem !important;
        min-height: 0 !important;
        max-height: none !important;
        -webkit-overflow-scrolling: touch !important;
        position: relative !important;
    }
    /* Ensure body doesn't interfere with modal scroll when nested modals are involved */
    body.modal-open #uploadMediaModal .modal-body {
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }
    /* Force scroll class for when cropper modal closes */
    #uploadMediaModal .modal-body.force-scroll {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        flex: 1 1 auto !important;
        min-height: 0 !important;
        -webkit-overflow-scrolling: touch !important;
    }
    /* Keep footer fixed */
    #uploadMediaModal .modal-footer {
        flex-shrink: 0 !important;
        border-top: 1px solid #dee2e6 !important;
        padding: 0.75rem !important;
    }
    /* Smooth scrolling */
    #uploadMediaModal .modal-body {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }
    #uploadMediaModal .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    #uploadMediaModal .modal-body::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 4px;
    }
    #uploadMediaModal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }
    #uploadMediaModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>
@endpush

@push('scripts')
<script>
// Make variables global so cropper modal can access them
window.currentFileForCropperUpload = null;
window.useCroppedImageUpload = false;

$(document).ready(function() {
    console.log('Upload modal script loaded');
    
    // Prevent multiple event handler attachments
    if ($('#uploadMediaForm').data('handler-attached')) {
        console.log('Handler already attached, skipping');
        return;
    }
    $('#uploadMediaForm').data('handler-attached', true);
    console.log('Handler attached to upload form');

    // File input preview with cropping support
    $('#mediaFile').on('change', function() {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            window.currentFileForCropperUpload = file;
            window.useCroppedImageUpload = false;
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result);
                    $('#filePreview').show();
                    $('#cropButtonContainer').show();
                    $('#imageOptionsGroup').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#filePreview').hide();
                $('#cropButtonContainer').hide();
                $('#imageOptionsGroup').hide();
                window.currentFileForCropperUpload = null;
            }
            
        // Update label
        $(this).next('.custom-file-label').text(file ? file.name : '{{ __('admin.Choose file') }}');
    });

    // Show watermark position when watermark is enabled
    $('#addWatermarkUpload').on('change', function() {
        $('#watermarkPositionGroupUpload').toggle(this.checked);
    });
    
    // Reset form and enable inputs when modal is shown
    $('#uploadMediaModal').on('show.bs.modal', function() {
            console.log('Modal opening, resetting form');
            $('#uploadMediaForm')[0].reset();
            $('#filePreview').hide();
            $('#cropButtonContainer').hide();
            $('#imageOptionsGroup').hide();
            $('#watermarkPositionGroupUpload').hide();
            currentFileForCropperUpload = null;
            useCroppedImageUpload = false;
            
        // Ensure all form elements are enabled
        $('#uploadMediaForm input, #uploadMediaForm textarea, #uploadMediaForm select, #uploadMediaForm button').prop('disabled', false);
        $('#uploadMediaForm button[type="submit"]').html('<i class="fas fa-upload"></i> {{ __('admin.Upload') }}');
    });
    
    // Force modal to front when shown
    $('#uploadMediaModal').on('shown.bs.modal', function() {
        console.log('Modal fully shown, forcing z-index');
        // Force very high z-index values
        $('#uploadMediaModal').css({
            'z-index': '9999',
            'display': 'block',
            'opacity': '1'
        });
        $('#uploadMediaModal .modal-dialog').css({
            'z-index': '10000',
            'pointer-events': 'auto'
        });
        $('#uploadMediaModal .modal-content').css({
            'z-index': '10001',
            'pointer-events': 'auto'
        });
        
        // Ensure backdrop is below
        $('.modal-backdrop').css('z-index', '1040');
        
        // Remove any overlay that might be blocking
        $('.modal-backdrop').not('#uploadMediaModal').each(function() {
            if ($(this).css('z-index') >= 9999) {
                $(this).css('z-index', '1040');
            }
        });
        
        // Setup scroll properly when modal is first shown
        setTimeout(function() {
            const modalDialog = $('#uploadMediaModal .modal-dialog');
            const modalContent = $('#uploadMediaModal .modal-content');
            const modalHeader = $('#uploadMediaModal .modal-header');
            const modalBody = $('#uploadMediaModal .modal-body');
            const modalFooter = $('#uploadMediaModal .modal-footer');
            
            if (modalDialog.length && modalContent.length && modalBody.length) {
                const viewportHeight = window.innerHeight;
                const maxModalHeight = Math.min(viewportHeight * 0.9, 600);
                
                modalDialog.css({
                    'display': 'flex',
                    'flex-direction': 'column',
                    'max-height': maxModalHeight + 'px'
                });
                
                modalContent.css({
                    'display': 'flex',
                    'flex-direction': 'column',
                    'max-height': maxModalHeight + 'px',
                    'overflow': 'hidden'
                });
                
                const headerHeight = modalHeader.outerHeight() || 0;
                const footerHeight = modalFooter.outerHeight() || 0;
                const bodyMaxHeight = maxModalHeight - headerHeight - footerHeight;
                
                if (modalBody[0]) {
                    modalBody.css({
                        'overflow-y': 'auto',
                        'overflow-x': 'hidden',
                        'flex': '1 1 auto',
                        'min-height': '0',
                        'max-height': bodyMaxHeight + 'px'
                    });
                    
                    // Force reflow
                    modalBody[0].offsetHeight;
                }
            }
        }, 100);
        
        // Test if modal is clickable
        $('#uploadMediaModal .modal-content').on('click', function(e) {
            console.log('Modal content clicked!', e.target);
        });
        
        // Make sure file input is accessible
        const fileInput = document.getElementById('mediaFile');
        if (fileInput) {
            fileInput.style.pointerEvents = 'auto';
            fileInput.style.zIndex = '10002';
            console.log('File input is accessible');
        } else {
            console.error('File input not found!');
        }
    });
    
    // Restore scroll when cropper modal closes
    $(document).on('hidden.bs.modal', '#imageCropperModalUpload', function() {
        console.log('Cropper modal closed, restoring upload modal scroll');
        
        // Ensure body still has modal-open class if upload modal is still open
        if ($('#uploadMediaModal').hasClass('show')) {
            $('body').addClass('modal-open');
        }
        
        // Small delay to ensure Bootstrap has finished its cleanup
        setTimeout(function() {
            // Ensure upload modal is still visible and scrollable
            if ($('#uploadMediaModal').hasClass('show') || $('#uploadMediaModal').is(':visible')) {
                const modalDialog = $('#uploadMediaModal .modal-dialog');
                const modalContent = $('#uploadMediaModal .modal-content');
                const modalHeader = $('#uploadMediaModal .modal-header');
                const modalBody = $('#uploadMediaModal .modal-body');
                const modalFooter = $('#uploadMediaModal .modal-footer');
                
                if (modalDialog.length && modalContent.length && modalBody.length) {
                    // Calculate proper heights
                    const viewportHeight = window.innerHeight;
                    const maxModalHeight = Math.min(viewportHeight * 0.9, 600);
                    
                    // Set explicit heights on dialog and content
                    modalDialog.css({
                        'display': 'flex',
                        'flex-direction': 'column',
                        'max-height': maxModalHeight + 'px',
                        'height': 'auto'
                    });
                    
                    modalContent.css({
                        'display': 'flex',
                        'flex-direction': 'column',
                        'max-height': maxModalHeight + 'px',
                        'height': 'auto',
                        'overflow': 'hidden'
                    });
                    
                    // Calculate body max height based on header and footer
                    const headerHeight = modalHeader.outerHeight() || 0;
                    const footerHeight = modalFooter.outerHeight() || 0;
                    const bodyMaxHeight = maxModalHeight - headerHeight - footerHeight;
                    
                    // Remove any blocking inline styles
                    if (modalBody[0]) {
                        modalBody[0].style.removeProperty('overflow');
                        modalBody[0].style.removeProperty('overflow-y');
                        modalBody[0].style.removeProperty('overflow-x');
                        modalBody[0].style.removeProperty('height');
                        modalBody[0].style.removeProperty('max-height');
                        
                        // Force restore scroll properties
                        modalBody.addClass('force-scroll');
                        modalBody.css({
                            'overflow-y': 'auto',
                            'overflow-x': 'hidden',
                            'flex': '1 1 auto',
                            'min-height': '0',
                            'max-height': bodyMaxHeight + 'px',
                            '-webkit-overflow-scrolling': 'touch',
                            'position': 'relative'
                        });
                        
                        // Force browser to recalculate
                        const bodyElement = modalBody[0];
                        bodyElement.offsetHeight;
                        
                        // Test scroll capability
                        const scrollHeight = bodyElement.scrollHeight;
                        const clientHeight = bodyElement.clientHeight;
                        
                        console.log('Scroll restoration:', {
                            'scrollHeight': scrollHeight,
                            'clientHeight': clientHeight,
                            'bodyMaxHeight': bodyMaxHeight,
                            'headerHeight': headerHeight,
                            'footerHeight': footerHeight
                        });
                        
                        // If content overflows, ensure scrollbar is visible
                        if (scrollHeight > clientHeight) {
                            // Trigger scrollbar by temporarily scrolling
                            const originalScrollTop = bodyElement.scrollTop;
                            bodyElement.scrollTop = 1;
                            bodyElement.scrollTop = originalScrollTop;
                            
                            // Force scrollbar to show (some browsers hide it)
                            bodyElement.style.overflowY = 'scroll';
                            setTimeout(function() {
                                bodyElement.style.overflowY = 'auto';
                            }, 100);
                        }
                    }
                }
            }
        }, 250);
    });
    
    // Clean up when modal is hidden
    $('#uploadMediaModal').on('hidden.bs.modal', function() {
        console.log('Modal closed, cleaning up');
        $('#uploadMediaForm')[0].reset();
        $('#filePreview').hide();
        $('#cropButtonContainer').hide();
        $('#imageOptionsGroup').hide();
        $('#watermarkPositionGroupUpload').hide();
        window.currentFileForCropperUpload = null;
        window.useCroppedImageUpload = false;
    });

    // Upload form submission with image processing
    $('#uploadMediaForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submit triggered');
        
        // Validate file is selected
        const fileInput = document.getElementById('mediaFile');
        if (!fileInput) {
            console.error('File input not found');
            Swal.fire({
                icon: 'error',
                title: '{{ __('admin.Error') }}',
                text: '{{ __('admin.File input not found') }}'
            });
            return false;
        }
        
        const file = fileInput.files[0];
        
        if (!file) {
            console.log('No file selected');
            Swal.fire({
                icon: 'warning',
                title: '{{ __('admin.Error') }}',
                text: '{{ __('admin.Please select a file to upload') }}'
            });
            return false;
        }
        
        console.log('File selected:', file.name, file.type);
        
        const formData = new FormData(this);
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        
        // Add image processing options (only if elements exist)
        if ($('#convertToWebpUpload').length) {
            formData.append('convert_to_webp', $('#convertToWebpUpload').is(':checked') ? 1 : 0);
        }
        if ($('#addWatermarkUpload').length) {
            formData.append('add_watermark', $('#addWatermarkUpload').is(':checked') ? 1 : 0);
        }
        if ($('#watermarkPositionUpload').length) {
            formData.append('watermark_position', $('#watermarkPositionUpload').val() || 'center');
        }
        if ($('#keepOriginalUpload').length) {
            formData.append('keep_original', $('#keepOriginalUpload').is(':checked') ? 1 : 0);
        }
        
        // Check if we need to use the image upload endpoint with processing
        const isImage = file && file.type.startsWith('image/');
        
        // Use image upload endpoint for images to get processing, otherwise use media library endpoint
        let uploadUrl;
        try {
            uploadUrl = isImage ? '{{ route('admin.upload-image') }}' : '{{ route('admin.media-library.store') }}';
        } catch (e) {
            console.error('Route error:', e);
            Swal.fire({
                icon: 'error',
                title: '{{ __('admin.Error') }}',
                text: '{{ __('admin.Route configuration error') }}'
            });
            return false;
        }
        
        if (!uploadUrl) {
            Swal.fire({
                icon: 'error',
                title: '{{ __('admin.Error') }}',
                text: '{{ __('admin.Upload URL not configured') }}'
            });
            return false;
        }
        
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('admin.Uploading') }}...');

        console.log('Uploading to:', uploadUrl);
        console.log('Form data keys:', Array.from(formData.keys()));

        $.ajax({
            url: uploadUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Upload success:', response);
                $('#uploadMediaModal').modal('hide');
                $('#uploadMediaForm')[0].reset();
                $('#filePreview').hide();
                $('#cropButtonContainer').hide();
                $('#imageOptionsGroup').hide();
                $('#watermarkPositionGroupUpload').hide();
                window.currentFileForCropperUpload = null;
                window.useCroppedImageUpload = false;
                $submitBtn.prop('disabled', false).html(originalText);
                
                Swal.fire('{{ __('admin.Success') }}', '{{ __('admin.Media uploaded successfully') }}', 'success')
                    .then(() => {
                        location.reload();
                    });
            },
            error: function(xhr, status, error) {
                console.error('Upload error:', status, error, xhr);
                let errorMessage = '{{ __('admin.Error uploading media') }}';
                
                // Handle Laravel validation errors (422 status)
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];
                    for (let field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            errorMessages.push(errors[field][0]);
                        }
                    }
                    errorMessage = errorMessages.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 0) {
                    errorMessage = '{{ __('admin.Network error. Please check your connection.') }}';
                } else if (xhr.status === 500) {
                    errorMessage = '{{ __('admin.Server error. Please try again later.') }}';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('admin.Error') }}',
                    html: errorMessage
                });
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush

