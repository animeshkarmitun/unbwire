<div class="modal fade" id="editMediaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.Edit Media') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMediaForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editMediaId" name="id">
                <input type="hidden" id="editMediaType" name="file_type">
                <div class="modal-body">
                    <div id="editMediaPreview" class="mb-3" style="display: none;">
                        <img id="editPreviewImage" src="" alt="Media Preview" class="img-fluid" style="max-height: 300px; width: 100%; object-fit: contain; border: 1px solid #ddd; border-radius: 4px; padding: 10px; background: #f8f9fa;">
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Title') }} <small class="text-muted">({{ __('admin.Optional') }})</small></label>
                        <input type="text" class="form-control" name="title" id="editTitle" placeholder="{{ __('admin.Enter title') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Alt Text') }} <small class="text-muted">({{ __('admin.Optional') }})</small></label>
                        <input type="text" class="form-control" name="alt_text" id="editAltText" placeholder="{{ __('admin.Enter alt text') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Caption') }} <small class="text-muted">({{ __('admin.Optional') }})</small></label>
                        <textarea class="form-control" name="caption" id="editCaption" rows="2" placeholder="{{ __('admin.Enter caption') }}"></textarea>
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Description') }} <small class="text-muted">({{ __('admin.Optional') }})</small></label>
                        <textarea class="form-control" name="description" id="editDescription" rows="3" placeholder="{{ __('Enter description') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('admin.Update') }}
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
        if ($('#editMediaForm').data('handler-attached')) {
            return;
        }
        $('#editMediaForm').data('handler-attached', true);

        $('#editMediaForm').on('submit', function(e) {
            e.preventDefault();
            
            const id = $('#editMediaId').val();
            const formData = $(this).serialize();
            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('admin.Updating') }}...');

            $.ajax({
                url: '/admin/media-library/' + id,
                method: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#editMediaModal').modal('hide');
                    $submitBtn.prop('disabled', false).html(originalText);
                    
                    Swal.fire('{{ __('admin.Success') }}', '{{ __('admin.Media updated successfully') }}', 'success')
                        .then(() => {
                            location.reload();
                        });
                },
                error: function(xhr) {
                    let errorMessage = '{{ __('admin.Error updating media') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire('{{ __('admin.Error') }}', errorMessage, 'error');
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
});
</script>

