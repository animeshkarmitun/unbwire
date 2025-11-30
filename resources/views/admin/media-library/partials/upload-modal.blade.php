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
        // Prevent multiple event handler attachments
        if ($('#uploadMediaForm').data('handler-attached')) {
            return;
        }
        $('#uploadMediaForm').data('handler-attached', true);

        // File input preview
        $('#mediaFile').on('change', function() {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result);
                    $('#filePreview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#filePreview').hide();
            }
            
            // Update label
            $(this).next('.custom-file-label').text(file ? file.name : '{{ __('admin.Choose file') }}');
        });

        // Upload form submission
        $('#uploadMediaForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('admin.Uploading') }}...');

            $.ajax({
                url: '{{ route('admin.media-library.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#uploadMediaModal').modal('hide');
                    $('#uploadMediaForm')[0].reset();
                    $('#filePreview').hide();
                    $submitBtn.prop('disabled', false).html(originalText);
                    
                    Swal.fire('{{ __('admin.Success') }}', '{{ __('admin.Media uploaded successfully') }}', 'success')
                        .then(() => {
                            location.reload();
                        });
                },
                error: function(xhr) {
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
});
</script>

