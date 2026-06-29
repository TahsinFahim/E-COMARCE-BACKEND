<x-app-layout>
    <div class="p-4">
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Status" id="filter_status" class="dt-filter-productRequestTable">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="fulfilled">Fulfilled</option>
                </x-form-select>
            </div>
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters" class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>
        </div>

        <x-data-table id="productRequestTable" title="Product Requests" icon="fa-solid fa-clipboard-list" :columns="['Image','Customer','Email','Phone','Product','Status','Date','Action']" :ajaxUrl="route('product-requests.dataTable')" :dtColumns="[
            ['data' => 'product_image_preview', 'orderable' => false, 'searchable' => false],
            ['data' => 'customer_name'],
            ['data' => 'customer_email'],
            ['data' => 'customer_phone'],
            ['data' => 'product_name'],
            ['data' => 'status'],
            ['data' => 'created_at'],
            ['data' => 'action', 'orderable' => false, 'searchable' => false],
        ]" :filters="[
            'status' => '#filter_status',
        ]" :exportButtons="true" />
    </div>

    {{-- View Modal --}}
    <div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeViewModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-bold text-gray-900 mb-4" id="modal-title">Product Request Details</h3>
                            <div id="viewContent" class="space-y-3 text-sm">
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Customer:</span>
                                    <span class="text-gray-900" id="view_name"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Email:</span>
                                    <span class="text-gray-900" id="view_email"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Phone:</span>
                                    <span class="text-gray-900" id="view_phone"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Product:</span>
                                    <span class="text-gray-900 font-semibold" id="view_product"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Description:</span>
                                    <span class="text-gray-900" id="view_description"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Quantity:</span>
                                    <span class="text-gray-900" id="view_quantity"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Expected Price:</span>
                                    <span class="text-gray-900" id="view_price"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Status:</span>
                                    <span class="text-gray-900" id="view_status"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Notes:</span>
                                    <span class="text-gray-900" id="view_notes"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Image:</span>
                                    <div id="view_image"></div>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-medium text-gray-600">Date:</span>
                                    <span class="text-gray-900" id="view_date"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeViewModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Modal --}}
    <div id="statusModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeStatusModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Change Request Status</h3>
                            <form id="statusForm" class="space-y-4">
                                <input type="hidden" id="status_request_id">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                                    <select id="new_status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="fulfilled">Fulfilled</option>
                                    </select>
                                </div>
                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" onclick="closeStatusModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Update Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-delete />

    @push('scripts')
    <script>
        function viewProductRequest(id) {
            $.get("{{ route('product-requests.show', ':id') }}".replace(':id', id), function(res) {
                if (res.status === 'success') {
                    const pr = res.product_request;
                    $('#view_name').text(pr.customer_name || '-');
                    $('#view_email').text(pr.customer_email || '-');
                    $('#view_phone').text(pr.customer_phone || '-');
                    $('#view_product').text(pr.product_name || '-');
                    $('#view_description').text(pr.product_description || '-');
                    $('#view_quantity').text(pr.quantity || '-');
                    $('#view_price').text(pr.expected_price ? '৳' + parseFloat(pr.expected_price).toLocaleString('en-BD') : '-');
                    $('#view_status').html(pr.status.charAt(0).toUpperCase() + pr.status.slice(1));
                    $('#view_notes').text(pr.notes || '-');
                    $('#view_date').text(pr.created_at ? new Date(pr.created_at).toLocaleDateString() : '-');
                    
                    if (pr.product_image) {
                        const imgUrl = pr.product_image.startsWith('http') ? pr.product_image : '/storage/' + pr.product_image;
                        $('#view_image').html('<img src="' + imgUrl + '" class="h-24 w-24 object-cover rounded border" />');
                    } else {
                        $('#view_image').text('No image');
                    }

                    $('#viewModal').removeClass('hidden');
                }
            });
        }

        function closeViewModal() {
            $('#viewModal').addClass('hidden');
        }

        function changeProductRequestStatus(id) {
            $('#status_request_id').val(id);
            $('#new_status').val('pending');
            $('#statusModal').removeClass('hidden');
        }

        function closeStatusModal() {
            $('#statusModal').addClass('hidden');
        }

        $('#statusForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#status_request_id').val();
            const status = $('#new_status').val();
            const url = "{{ route('product-requests.status', ':id') }}".replace(':id', id);

            $.post(url, { status: status, _token: '{{ csrf_token() }}' }, function(res) {
                if (res.status === 'success') {
                    Toastify({
                        text: res.message,
                        duration: 3000,
                        gravity: 'bottom',
                        position: 'right',
                        style: { background: 'linear-gradient(135deg, #16a34a, #4ade80)' },
                    }).showToast();
                    closeStatusModal();
                    $('#productRequestTable').DataTable().ajax.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }).fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
            });
        });

        $(document).ready(function() {
            $('#resetFilters').on('click', function() {
                $('#filter_status').val('');
                $('#productRequestTable').DataTable().ajax.reload();
            });
        });
    </script>
    @endpush
</x-app-layout>