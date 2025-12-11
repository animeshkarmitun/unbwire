<!-- Image Cropper Modal -->
<div class="modal fade" id="imageCropperModal" tabindex="-1" role="dialog" aria-labelledby="imageCropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageCropperModalLabel">{{ __('Crop Image') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="img-container">
                            <img id="cropperImage" src="" alt="Crop me" style="max-width: 100%;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Image Options') }}</label>
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
                        <div class="form-group" id="watermarkPositionGroup" style="display: none;">
                            <label>{{ __('Watermark Position') }}</label>
                            <select class="form-control" id="watermarkPosition">
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
                        <div class="form-group">
                            <button type="button" class="btn btn-secondary btn-sm" id="resetCrop">{{ __('Reset') }}</button>
                            <button type="button" class="btn btn-primary btn-sm" id="cropImage">{{ __('Crop & Upload') }}</button>
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
    .img-container {
        max-height: 500px;
        overflow: hidden;
    }
    .cropper-container {
        direction: ltr;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    let cropper;
    let currentFileInput;
    let currentCallback;

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

        // Initialize cropper when modal is shown
        $('#imageCropperModal').on('shown.bs.modal', function() {
            const image = document.getElementById('cropperImage');
            if (cropper) {
                cropper.destroy();
            }
            cropper = new Cropper(image, {
                aspectRatio: NaN, // Free aspect ratio
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.8,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        });

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

        // Crop and upload
        $('#cropImage').on('click', function() {
            if (!cropper || !currentFileInput) {
                return;
            }

            const canvas = cropper.getCroppedCanvas({
                width: cropper.getCroppedCanvas().width,
                height: cropper.getCroppedCanvas().height,
            });

            if (!canvas) {
                alert('Could not get cropped canvas');
                return;
            }

            // Convert canvas to blob
            canvas.toBlob(function(blob) {
                const formData = new FormData();
                formData.append('file', blob, currentFileInput.files[0].name);
                formData.append('alt', '');
                formData.append('caption', '');
                formData.append('convert_to_webp', $('#convertToWebp').is(':checked') ? 1 : 0);
                formData.append('add_watermark', $('#addWatermark').is(':checked') ? 1 : 0);
                formData.append('watermark_position', $('#watermarkPosition').val());
                
                // Get crop data
                const cropData = cropper.getData();
                formData.append('crop_data[x]', Math.round(cropData.x));
                formData.append('crop_data[y]', Math.round(cropData.y));
                formData.append('crop_data[width]', Math.round(cropData.width));
                formData.append('crop_data[height]', Math.round(cropData.height));
                formData.append('crop_data[rotate]', cropData.rotate || 0);

                // Upload
                $.ajax({
                    url: '{{ route("admin.upload-image") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#imageCropperModal').modal('hide');
                        if (currentCallback && typeof currentCallback === 'function') {
                            currentCallback(response);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error uploading image';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        alert(errorMsg);
                    }
                });
            }, 'image/jpeg', 0.9);
        });
    });
    });

    // Function to open cropper modal
    function openImageCropper(fileInput, callback) {
        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            return;
        }

        const file = fileInput.files[0];
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }

        // Ensure jQuery is available
        if (typeof jQuery === 'undefined') {
            alert('jQuery is not loaded. Please refresh the page.');
            return;
        }

        currentFileInput = fileInput;
        currentCallback = callback;

        const reader = new FileReader();
        reader.onload = function(e) {
            jQuery('#cropperImage').attr('src', e.target.result);
            jQuery('#imageCropperModal').modal('show');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush

