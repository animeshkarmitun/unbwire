<style>
    /* High z-index to ensure modal appears above backdrop and other elements */
    #editMediaModal {
        z-index: 1060 !important;
    }
    #editMediaModal.show {
        z-index: 1060 !important;
    }
    #editMediaModal.modal {
        z-index: 1060 !important;
    }
    #editMediaModal .modal-dialog {
        z-index: 1061 !important;
        pointer-events: auto !important;
        position: relative !important;
    }
    #editMediaModal .modal-content {
        z-index: 1062 !important;
        pointer-events: auto !important;
        position: relative !important;
    }
    body.modal-open #editMediaModal {
        z-index: 1060 !important;
    }
    /* Ensure backdrop is below this modal */
    body.modal-open .modal-backdrop {
        z-index: 1040 !important;
    }
    /* Ensure this modal is above backdrop */
    body.modal-open #editMediaModal.show {
        z-index: 1060 !important;
    }
</style>
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
            if (!id) {
                Swal.fire('{{ __('admin.Error') }}', '{{ __('admin.Invalid media ID') }}', 'error');
                return;
            }
            
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
                    console.error('Update media error:', xhr);
                    let errorMessage = '{{ __('admin.Error updating media') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = '{{ __('admin.Media not found') }}';
                    } else if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }
                    } else if (xhr.status === 500) {
                        errorMessage = '{{ __('admin.Server error occurred') }}';
                    }
                    
                    Swal.fire('{{ __('admin.Error') }}', errorMessage, 'error');
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
        
        // Ensure modal is visible when shown and above backdrop
        $('#editMediaModal').on('shown.bs.modal', function() {
            // Force high z-index values
            $(this).css({
                'z-index': '1060',
                'display': 'block',
                'opacity': '1'
            });
            $(this).find('.modal-dialog').css({
                'z-index': '1061',
                'pointer-events': 'auto',
                'position': 'relative'
            });
            $(this).find('.modal-content').css({
                'z-index': '1062',
                'pointer-events': 'auto',
                'position': 'relative'
            });
            
            // Ensure backdrop is below this modal
            $('.modal-backdrop').css('z-index', '1040');
            
            // Remove any other modals' high z-index that might interfere
            $('.modal').not('#editMediaModal').each(function() {
                if ($(this).hasClass('show') && $(this).attr('id') !== 'editMediaModal') {
                    const currentZ = parseInt($(this).css('z-index')) || 0;
                    if (currentZ >= 1060) {
                        $(this).css('z-index', '1050');
                    }
                }
            });
        });
    });
});
</script>

