<x-app-layout>
    <x-entity-crud
        id="purchase-order"
        title="Purchase Orders"
        icon="fa-solid fa-file-invoice"
        :columns="['PO Number','Supplier','Store','Status','Payment','Total','Created At','Action']"
        :dtColumns="[
            ['data' => 'po_number'],
            ['data' => 'supplier_name'],
            ['data' => 'store_name'],
            ['data' => 'status', 'orderable' => false, 'searchable' => false],
            ['data' => 'payment_status', 'orderable' => false, 'searchable' => false],
            ['data' => 'total_amount'],
            ['data' => 'created_at'],
            ['data' => 'action', 'orderable' => false, 'searchable' => false],
        ]"
        ajaxUrl="{{ route('purchase-orders.dataTable') }}"
        :exportButtons="false"
        :order="[[6, 'desc']]"
    />
</x-app-layout>
