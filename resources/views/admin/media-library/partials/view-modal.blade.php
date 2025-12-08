<style>
    /* High z-index to ensure modal appears above backdrop and other elements */
    #viewMediaModal {
        z-index: 1060 !important;
    }
    #viewMediaModal.show {
        z-index: 1060 !important;
    }
    #viewMediaModal.modal {
        z-index: 1060 !important;
    }
    #viewMediaModal .modal-dialog {
        z-index: 1061 !important;
        pointer-events: auto !important;
        position: relative !important;
    }
    #viewMediaModal .modal-content {
        z-index: 1062 !important;
        pointer-events: auto !important;
        position: relative !important;
    }
    body.modal-open #viewMediaModal {
        z-index: 1060 !important;
    }
    /* Ensure backdrop is below this modal */
    body.modal-open .modal-backdrop {
        z-index: 1040 !important;
    }
    /* Ensure this modal is above backdrop */
    body.modal-open #viewMediaModal.show {
        z-index: 1060 !important;
    }
    #viewMediaModal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
<div class="modal fade" id="viewMediaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Media Details') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewMediaContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Ensure modal is visible when shown and above backdrop
    $('#viewMediaModal').on('shown.bs.modal', function() {
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
        $('.modal').not('#viewMediaModal').each(function() {
            if ($(this).hasClass('show') && $(this).attr('id') !== 'viewMediaModal') {
                const currentZ = parseInt($(this).css('z-index')) || 0;
                if (currentZ >= 1060) {
                    $(this).css('z-index', '1050');
                }
            }
        });
    });
});
</script>

