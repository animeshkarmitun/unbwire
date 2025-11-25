<div class="modal fade" id="editMediaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
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
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('admin.Title') }}</label>
                        <input type="text" class="form-control" name="title" id="editTitle">
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Alt Text') }}</label>
                        <input type="text" class="form-control" name="alt_text" id="editAltText">
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Caption') }}</label>
                        <textarea class="form-control" name="caption" id="editCaption" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Description') }}</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="control-label">{{ __('admin.Featured') }}</div>
                        <label class="custom-switch mt-2">
                            <input type="checkbox" name="is_featured" id="editIsFeatured" class="custom-switch-input" value="1">
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('admin.Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
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
</script>

