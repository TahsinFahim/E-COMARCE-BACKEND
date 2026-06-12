<x-app-layout>
    <div class="p-4">
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Brand" id="filter_brand" class="dt-filter-productTable">
                    <option value="">All Brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </x-form-select>
            </div>
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Category" id="filter_category" class="dt-filter-productTable">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-form-select>
            </div>
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters" class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>
        </div>

        <x-data-table id="productTable" title="Product Catalog" icon="fa-solid fa-boxes" buttonLink="{{ route('products.create') }}" buttonText="Add New Product" :columns="['Brand','SKU','Name','Type','Status','Visibility','Created At','Action']" :ajaxUrl="route('products.dataTable')" :dtColumns="[
            ['data' => 'brand.name'],
            ['data' => 'slug'],
            ['data' => 'name'],
            ['data' => 'product_type'],
            ['data' => 'status'],
            ['data' => 'visibility'],
            ['data' => 'created_at'],
            ['data' => 'action', 'orderable' => false, 'searchable' => false],
        ]" :filters="[
            'brand_id' => '#filter_brand',
            'category_id' => '#filter_category',
        ]" :exportButtons="true" />
    </div>

    <x-confirm-delete />

    @push('scripts')
        <script>
            function productEdit(id) {
                let editUrl = "{{ route('products.edit', ':id') }}".replace(':id', id);
                window.location.href = editUrl;
            }

            function productDelete(id) {
                let deleteUrl = "{{ route('products.destroy', ':id') }}".replace(':id', id);
                let tableId = '#productTable';
                confirmAndDelete(deleteUrl, tableId);
            }

            $(document).ready(function() {
                $('#resetFilters').on('click', function() {
                    $('#filter_brand').val('');
                    $('#filter_category').val('');
                    $('#productTable').DataTable().ajax.reload();
                });
            });
        </script>
    @endpush
</x-app-layout>