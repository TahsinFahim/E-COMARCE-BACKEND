<x-app-layout>
    <x-entity-crud
        id="inventory-stock"
        title="Inventory Stock"
        icon="fa-solid fa-boxes"
        :columns="['Location','Store','Qty On Hand','Qty Reserved','Available','Reorder Point','Low Stock','Last Updated','Action']"
        :dtColumns="[
            ['data' => 'location_name'],
            ['data' => 'store_name'],
            ['data' => 'quantity_on_hand'],
            ['data' => 'quantity_reserved'],
            ['data' => 'available_quantity'],
            ['data' => 'reorder_point'],
            ['data' => 'low_stock'],
            ['data' => 'updated_at'],
            ['data' => 'action', 'orderable' => false, 'searchable' => false],
        ]"
        ajaxUrl="{{ route('inventory-stock.dataTable') }}"
        storeUrl="{{ route('inventory-stock.store') }}"
        updateUrl="{{ route('inventory-stock.update', ':id') }}"
        showUrl="{{ route('inventory-stock.show', ':id') }}"
        destroyUrl="{{ route('inventory-stock.destroy', ':id') }}"
        drawerTitle="Stock Record"
        dataKey="stock"
        idField="stock_id"
        :order="[[7, 'desc']]"
    >
        <div class="mb-4">
            <x-form-select label="Location" name="location_id" id="stock_location_id">
                @foreach($locations ?? [] as $location)
                    <option value="{{ $location['id'] }}">{{ $location['name'] }} ({{ $location['store']['name'] ?? '' }})</option>
                @endforeach
                <option value="" disabled selected>Select a location</option>
            </x-form-select>
        </div>
        <div class="mb-4">
            <x-form-input label="Quantity On Hand" name="quantity_on_hand" id="stock_quantity_on_hand" type="number" min="0" placeholder="0" required />
        </div>
        <div class="mb-4">
            <x-form-input label="Quantity Reserved" name="quantity_reserved" id="stock_quantity_reserved" type="number" min="0" placeholder="0" required />
        </div>
        <div class="mb-4">
            <x-form-input label="Reorder Point" name="reorder_point" id="stock_reorder_point" type="number" min="0" placeholder="0" required />
        </div>
    </x-entity-crud>

    @push('scripts')
    <script>
        window.fillInventoryStockForm = function(data) {
            $('#stock_location_id').val(data.location_id);
            $('#stock_quantity_on_hand').val(data.quantity_on_hand);
            $('#stock_quantity_reserved').val(data.quantity_reserved);
            $('#stock_reorder_point').val(data.reorder_point);
        };
    </script>
    @endpush
</x-app-layout>