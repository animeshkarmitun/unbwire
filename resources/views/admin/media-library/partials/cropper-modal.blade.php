<!-- Image Cropper Modal for Media Library Upload -->
<div class="modal fade" id="imageCropperModalUpload" tabindex="-1" role="dialog" aria-labelledby="imageCropperModalUploadLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageCropperModalUploadLabel">{{ __('Crop Image') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="row">
                    <div class="col-md-9">
                        <div class="cropper-wrapper" style="background: #f5f5f5; border: 2px solid #ddd; border-radius: 4px; padding: 10px; min-height: 500px; max-height: 70vh; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            <div class="img-container" id="cropperContainerUpload" style="width: 100%; max-width: 100%; position: relative; max-height: 70vh;">
                                <img id="cropperImageUpload" src="" alt="Crop me" style="display: block; max-width: 100%; max-height: 70vh;">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="font-weight-bold mb-2 d-block">{{ __('Aspect Ratio:') }}</label>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary active" id="aspectRatioFreeUpload">{{ __('Free') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="aspectRatio1_1Upload">1:1</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="aspectRatio16_9Upload">16:9</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="aspectRatio4_3Upload">4:3</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="aspectRatio3_2Upload">3:2</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Crop Actions') }}</label>
                            <button type="button" class="btn btn-secondary btn-sm btn-block" id="resetCropUpload">
                                <i class="fas fa-undo"></i> {{ __('Reset') }}
                            </button>
                            <button type="button" class="btn btn-primary btn-sm btn-block mt-2" id="cropImageUpload">
                                <i class="fas fa-check"></i> {{ __('Crop & Continue') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block mt-2" data-dismiss="modal">
                                <i class="fas fa-times"></i> {{ __('Cancel') }}
                            </button>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Crop Info') }}</label>
                            <div class="small text-muted">
                                <div>Width: <span id="cropWidthUpload">-</span>px</div>
                                <div>Height: <span id="cropHeightUpload">-</span>px</div>
                                <div>X: <span id="cropXUpload">-</span>px</div>
                                <div>Y: <span id="cropYUpload">-</span>px</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    #imageCropperModalUpload {
        z-index: 10010 !important;
    }
    #imageCropperModalUpload.show {
        z-index: 10010 !important;
    }
    #imageCropperModalUpload .modal-dialog {
        z-index: 10011 !important;
        max-width: 95%;
        pointer-events: auto !important;
    }
    #imageCropperModalUpload .modal-content {
        z-index: 10012 !important;
        pointer-events: auto !important;
    }
    /* Ensure cropper modal appears above upload modal */
    body.modal-open #imageCropperModalUpload {
        z-index: 10010 !important;
    }
    body.modal-open #imageCropperModalUpload.show {
        z-index: 10010 !important;
    }
    .cropper-wrapper {
        background: #f5f5f5;
        border: 2px solid #ddd;
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
    .cropper-point.point-se,
    .cropper-point.point-sw,
    .cropper-point.point-nw,
    .cropper-point.point-ne {
        width: 14px !important;
        height: 14px !important;
    }
    .cropper-point.point-n,
    .cropper-point.point-s {
        cursor: ns-resize !important;
    }
    .cropper-point.point-e,
    .cropper-point.point-w {
        cursor: ew-resize !important;
    }
    .btn-group .btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .modal-backdrop.show {
        z-index: 1001;
    }
    #imageCropperModalUpload.show {
        z-index: 1060;
    }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    let cropperUpload;
    let cropperLibraryLoadedUpload = false;
    
    // Check if Cropper library is loaded
    function checkCropperLibraryUpload() {
        if (typeof Cropper !== 'undefined') {
            cropperLibraryLoadedUpload = true;
            return true;
        }
        return false;
    }
    
    // Wait for Cropper library to load
    if (typeof Cropper === 'undefined') {
        const checkInterval = setInterval(function() {
            if (checkCropperLibraryUpload()) {
                clearInterval(checkInterval);
            }
        }, 100);
        
        setTimeout(function() {
            clearInterval(checkInterval);
            if (!cropperLibraryLoadedUpload) {
                console.error('Cropper library failed to load');
            }
        }, 5000);
    } else {
        cropperLibraryLoadedUpload = true;
    }

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
        // Open cropper modal for upload
        $('#openCropperBtnUpload').on('click', function() {
            if (!window.currentFileForCropperUpload) {
                alert('{{ __('Please select an image first') }}');
                return;
            }
            
            if (!checkCropperLibraryUpload()) {
                alert('{{ __('Image cropper library is loading. Please wait a moment and try again.') }}');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageSrc = e.target.result;
                const imageElement = document.getElementById('cropperImageUpload');
                
                imageElement.src = '';
                $('#imageCropperModalUpload').modal('show');
                
                setTimeout(function() {
                    $(imageElement).css({
                        'opacity': '1',
                        'display': 'block',
                        'visibility': 'visible'
                    });
                    
                    imageElement.src = imageSrc;
                    
                    if (imageElement.complete && imageElement.naturalWidth > 0) {
                        setTimeout(function() {
                            initializeCropperUpload();
                        }, 300);
                    } else {
                        imageElement.onload = function() {
                            setTimeout(function() {
                                initializeCropperUpload();
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
            reader.readAsDataURL(window.currentFileForCropperUpload);
        });

        // Initialize cropper function
        function initializeCropperUpload() {
            const image = document.getElementById('cropperImageUpload');
            
            if (!image) {
                console.error('Cropper image element not found');
                return;
            }
            
            if (image.complete && image.naturalWidth > 0) {
                setTimeout(function() {
                    createCropperUpload(image);
                }, 100);
            } else {
                image.onload = function() {
                    setTimeout(function() {
                        createCropperUpload(image);
                    }, 100);
                };
                image.onerror = function() {
                    alert('{{ __('Failed to load image') }}');
                };
            }
        }

        function createCropperUpload(imageElement) {
            if (cropperUpload) {
                try {
                    cropperUpload.destroy();
                } catch (e) {
                    console.warn('Error destroying cropper:', e);
                }
                cropperUpload = null;
            }
            
            if (typeof Cropper === 'undefined') {
                alert('{{ __('Cropper library not loaded. Please refresh the page.') }}');
                return;
            }
            
            if (!imageElement || !imageElement.src) {
                alert('{{ __('Image not loaded. Please try again.') }}');
                return;
            }
            
            try {
                if (imageElement.naturalWidth === 0 || imageElement.naturalHeight === 0) {
                    console.error('Image has no dimensions');
                    alert('{{ __('Image failed to load. Please try again.') }}');
                    return;
                }
                
                $(imageElement).css({
                    'opacity': '1',
                    'display': 'block',
                    'max-width': '100%',
                    'max-height': '70vh'
                });
                
                console.log('Creating cropper with image dimensions:', imageElement.naturalWidth, 'x', imageElement.naturalHeight);
                
                cropperUpload = new Cropper(imageElement, {
                    aspectRatio: NaN,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: true,
                    minCanvasWidth: 0,
                    minCanvasHeight: 0,
                    minCropBoxWidth: 10,
                    minCropBoxHeight: 10,
                    responsive: true,
                    checkOrientation: true,
                    modal: true,
                    background: true,
                    zoomable: true,
                    scalable: true,
                    rotatable: false,
                    movable: true,
                    ready: function() {
                        console.log('Cropper initialized successfully');
                        updateCropInfoUpload();
                        setTimeout(function() {
                            const viewBox = document.querySelector('#imageCropperModalUpload .cropper-view-box');
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
                        updateCropInfoUpload();
                    },
                    cropstart: function() {
                        console.log('Crop started');
                    },
                    cropmove: function() {
                        updateCropInfoUpload();
                    },
                    cropend: function() {
                        console.log('Crop ended');
                        updateCropInfoUpload();
                    }
                });
                
                console.log('Cropper instance created successfully');
            } catch (error) {
                console.error('Error creating cropper:', error);
                alert('{{ __('Failed to initialize image cropper. Please try again.') }}: ' + error.message);
            }
        }
        
        // Update crop information display
        function updateCropInfoUpload() {
            if (!cropperUpload) return;
            
            const cropData = cropperUpload.getData();
            $('#cropWidthUpload').text(Math.round(cropData.width));
            $('#cropHeightUpload').text(Math.round(cropData.height));
            $('#cropXUpload').text(Math.round(cropData.x));
            $('#cropYUpload').text(Math.round(cropData.y));
        }
        
        // Aspect ratio handlers
        $('#aspectRatioFreeUpload').on('click', function() {
            if (cropperUpload) {
                cropperUpload.setAspectRatio(NaN);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
        
        $('#aspectRatio1_1Upload').on('click', function() {
            if (cropperUpload) {
                cropperUpload.setAspectRatio(1);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
        
        $('#aspectRatio16_9Upload').on('click', function() {
            if (cropperUpload) {
                cropperUpload.setAspectRatio(16 / 9);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
        
        $('#aspectRatio4_3Upload').on('click', function() {
            if (cropperUpload) {
                cropperUpload.setAspectRatio(4 / 3);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
        
        $('#aspectRatio3_2Upload').on('click', function() {
            if (cropperUpload) {
                cropperUpload.setAspectRatio(3 / 2);
                $(this).addClass('active').siblings().removeClass('active');
            }
        });

        // Initialize cropper when modal is shown
        $('#imageCropperModalUpload').on('shown.bs.modal', function() {
            // Force z-index to ensure it's above upload modal
            setTimeout(function() {
                $('#imageCropperModalUpload').css({
                    'z-index': '10010',
                    'display': 'block',
                    'opacity': '1'
                });
                $('#imageCropperModalUpload .modal-dialog').css({
                    'z-index': '10011',
                    'pointer-events': 'auto'
                });
                $('#imageCropperModalUpload .modal-content').css({
                    'z-index': '10012',
                    'pointer-events': 'auto'
                });
                
                // Ensure backdrop is below cropper modal
                $('.modal-backdrop').each(function() {
                    if ($(this).css('z-index') < 10010) {
                        $(this).css('z-index', '1040');
                    }
                });
            }, 100);
            
            setTimeout(function() {
                const image = document.getElementById('cropperImageUpload');
                if (image && image.src && !cropperUpload) {
                    initializeCropperUpload();
                }
            }, 200);
        });

        // Destroy cropper when modal is hidden
        $('#imageCropperModalUpload').on('hidden.bs.modal', function() {
            if (cropperUpload) {
                cropperUpload.destroy();
                cropperUpload = null;
            }
            
            // Ensure body still has modal-open if upload modal is still open
            if ($('#uploadMediaModal').hasClass('show')) {
                $('body').addClass('modal-open');
            }
            
            // Restore upload modal scroll after a short delay
            setTimeout(function() {
                if ($('#uploadMediaModal').hasClass('show') || $('#uploadMediaModal').is(':visible')) {
                    const modalBody = $('#uploadMediaModal .modal-body');
                    if (modalBody.length && modalBody[0]) {
                        // Remove any blocking styles
                        modalBody[0].style.removeProperty('overflow');
                        modalBody[0].style.removeProperty('overflow-y');
                        modalBody[0].style.removeProperty('overflow-x');
                        
                        // Force restore
                        modalBody.addClass('force-scroll');
                        modalBody.css({
                            'overflow-y': 'auto',
                            'overflow-x': 'hidden',
                            '-webkit-overflow-scrolling': 'touch'
                        });
                        
                        // Trigger reflow and test scroll
                        const bodyEl = modalBody[0];
                        bodyEl.offsetHeight;
                        if (bodyEl.scrollHeight > bodyEl.clientHeight) {
                            bodyEl.scrollTop = 1;
                            bodyEl.scrollTop = 0;
                        }
                    }
                }
            }, 150);
        });

        // Reset crop
        $('#resetCropUpload').on('click', function() {
            if (cropperUpload) {
                cropperUpload.reset();
            }
        });

        // Crop and continue
        $('#cropImageUpload').on('click', function() {
            if (!cropperUpload) {
                alert('{{ __('Cropper not initialized. Please wait a moment and try again.') }}');
                return;
            }

            try {
                const canvas = cropperUpload.getCroppedCanvas({
                    width: cropperUpload.getCroppedCanvas().width,
                    height: cropperUpload.getCroppedCanvas().height,
                });

                if (!canvas) {
                    alert('{{ __('Could not get cropped canvas') }}');
                    return;
                }

                canvas.toBlob(function(blob) {
                    if (!blob) {
                        alert('{{ __('Failed to create cropped image') }}');
                        return;
                    }
                    
                    // Get original file name or use default
                    const originalFileName = (window.currentFileForCropperUpload && window.currentFileForCropperUpload.name) 
                        ? window.currentFileForCropperUpload.name 
                        : 'cropped-image.jpg';
                    
                    // Extract name without extension and add timestamp
                    const nameWithoutExt = originalFileName.replace(/\.[^/.]+$/, '');
                    const timestamp = new Date().getTime();
                    const newFileName = nameWithoutExt + '-cropped-' + timestamp + '.jpg';
                    
                    const file = new File([blob], newFileName, { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    
                    // Support both upload-modal (mediaFile/previewImage) and media-modal (quickUploadFile/quickPreviewImage)
                    const fileInput = document.getElementById('mediaFile') || document.getElementById('quickUploadFile');
                    const previewImage = document.getElementById('previewImage') || document.getElementById('quickPreviewImage');
                    
                    if (fileInput) {
                        fileInput.files = dataTransfer.files;
                        
                        if (previewImage) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                $(previewImage).attr('src', e.target.result);
                            };
                            reader.readAsDataURL(file);
                        }
                        
                        window.currentFileForCropperUpload = file;
                        window.useCroppedImageUpload = true;
                        $('#imageCropperModalUpload').modal('hide');
                    } else {
                        alert('{{ __('File input not found') }}');
                    }
                }, 'image/jpeg', 0.9);
            } catch (error) {
                console.error('Crop error:', error);
                alert('{{ __('An error occurred while cropping the image') }}');
            }
        });
        
        // Set default aspect ratio to free
        $('#aspectRatioFreeUpload').addClass('active');
    });
    });
</script>
@endpush

