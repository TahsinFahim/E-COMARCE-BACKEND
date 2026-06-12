<x-app-layout>
    <x-entity-crud
        id="category"
        title="Category Catalog"
        icon="fa-solid fa-tags"
        :columns="['Name','Parent','Status','Created At','Action']"
        :dtColumns="[
            ['data' => 'name'],
            ['data' => 'parent'],
            ['data' => 'status'],
            ['data' => 'created_at'],
            ['data' => 'action', 'orderable' => false, 'searchable' => false],
        ]"
        ajaxUrl="{{ route('categories.dataTable') }}"
        storeUrl="{{ route('categories.store') }}"
        updateUrl="{{ route('categories.update', ':id') }}"
        showUrl="{{ route('categories.show', ':id') }}"
        destroyUrl="{{ route('categories.destroy', ':id') }}"
        drawerTitle="Category"
        dataKey="category"
        idField="category_id"
    >
        <div class="mb-4">
            <x-form-select label="Parent Category" name="parent_id" id="category_parent_id">
                <option value="">None</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </x-form-select>
        </div>
        <div class="mb-4">
            <x-form-input label="Name" name="name" id="category_name" placeholder="Category Name" required />
        </div>
        <div class="mb-4">
            <x-form-input label="Slug" name="slug" id="category_slug" placeholder="Category Slug" required />
        </div>
        <div class="mb-4">
            <x-form-input label="Image URL" name="image_url" id="category_image_url" placeholder="Image URL" />
        </div>
        <div class="mb-4">
            <x-form-input label="Sort Order" name="sort_order" id="category_sort_order" type="number" placeholder="Sort Order" />
        </div>
        <div class="mb-4">
            <x-form-select label="Status" name="status" id="category_status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </x-form-select>
        </div>
        <div class="mb-4">
            <x-form-textarea label="Description" name="description" id="category_description" placeholder="Category description" rows="4" />
        </div>
    </x-entity-crud>

    @push('scripts')
    <script>
        window.fillCategoryForm = function(data) {
            $('#category_parent_id').val(data.parent_id || '');
            $('#category_name').val(data.name);
            $('#category_slug').val(data.slug);
            $('#category_image_url').val(data.image_url || '');
            $('#category_sort_order').val(data.sort_order || '');
            $('#category_status').val(data.status);
            $('#category_description').val(data.description || '');
        };

        $(document).ready(function() {
            $('#category_name').on('input', function() {
                if ($('#category_id').val() === '') {
                    let slug = $(this).val().toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
                    $('#category_slug').val(slug);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>