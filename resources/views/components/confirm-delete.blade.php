@push('scripts')
<script>
/**
 * Reusable delete confirmation helper.
 * Usage: confirmAndDelete(deleteUrl, tableSelector);
 * deleteUrl: the full URL to the destroy endpoint (e.g. "/products/5" or "/stores/3")
 * tableSelector: jQuery selector for the DataTable to reload after deletion
 */
window.confirmAndDelete = function(deleteUrl, tableSelector) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#4b5563',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.status === 'success') {
                        Toastify({
                            text: res.message || 'Deleted successfully',
                            duration: 3000,
                            gravity: 'bottom',
                            position: 'right',
                            style: { background: 'linear-gradient(135deg, #dc2626, #f87171)' },
                        }).showToast();
                        $(tableSelector).DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message || 'Error deleting', 'error');
                    }
                },
                error: function() { Swal.fire('Error', 'Server communication error.', 'error'); }
            });
        }
    });
};
</script>
@endpush