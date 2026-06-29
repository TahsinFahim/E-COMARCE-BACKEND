<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Homepage CTAs') }}
        </h2>
    </x-slot>

    <x-entity-crud
        id="cta"
        title="Homepage CTAs"
        icon="fa-solid fa-bullhorn"
        :columns="['Image','Title','Subtitle','Button Text','Sort Order','Status','Created At','Action']"
        :dtColumns="[
            ['data' => 'image', 'orderable' => false, 'searchable' => false],
            ['data' => 'title'],
            ['data' => 'subtitle'],
            ['data' => 'button_text'],
            ['data' => 'sort_order'],
            ['data' => 'status'],
            ['data' => 'created_at'],
            ['data' => 'action', 'orderable' => false, 'searchable' => false],
        ]"
        ajaxUrl="{{ route('frontend.ctas.dataTable') }}"
        storeUrl="{{ route('frontend.ctas.store') }}"
        updateUrl="{{ route('frontend.ctas.update', ':id') }}"
        showUrl="{{ route('frontend.ctas.show', ':id') }}"
        destroyUrl="{{ route('frontend.ctas.destroy', ':id') }}"
        drawerTitle="CTA"
        dataKey="cta"
        idField="cta_id"
        :order="[[4, 'asc']]"
    >
        <div class="mb-4">
            <x-form-input label="Title" name="title" id="cta_title" placeholder="CTA Title" required />
        </div>
        <div class="mb-4">
            <x-form-input label="Subtitle" name="subtitle" id="cta_subtitle" placeholder="CTA Subtitle" />
        </div>
        <div class="mb-4">
            <x-form-textarea label="Description" name="description" id="cta_description" placeholder="CTA Description" rows="3" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1" for="cta_image">CTA Image</label>
            <input type="file" name="image" id="cta_image" accept="image/*"
                class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            <div id="ctaImagePreview" class="hidden mt-2">
                <img src="" alt="CTA preview" class="w-32 h-20 object-cover rounded border" />
            </div>
        </div>
        <div class="mb-4">
            <x-form-input label="Button Text" name="button_text" id="cta_button_text" placeholder="e.g. Shop Now" required />
        </div>
        <div class="mb-4">
            <x-form-input label="Button Link" name="button_link" id="cta_button_link" placeholder="e.g. /sale/summer" required />
        </div>

        <!-- Color fields in a 2-column grid -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="cta_background_color">Background Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" name="background_color" id="cta_background_color" value="#f8f9fa"
                        class="w-10 h-10 p-0.5 border border-gray-300 rounded cursor-pointer" />
                    <input type="text" id="cta_background_color_hex" value="#f8f9fa" maxlength="20"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="cta_text_color">Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" name="text_color" id="cta_text_color" value="#1f2937"
                        class="w-10 h-10 p-0.5 border border-gray-300 rounded cursor-pointer" />
                    <input type="text" id="cta_text_color_hex" value="#1f2937" maxlength="20"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="cta_button_color">Button Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" name="button_color" id="cta_button_color" value="#1e3a8a"
                        class="w-10 h-10 p-0.5 border border-gray-300 rounded cursor-pointer" />
                    <input type="text" id="cta_button_color_hex" value="#1e3a8a" maxlength="20"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="cta_button_text_color">Button Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" name="button_text_color" id="cta_button_text_color" value="#ffffff"
                        class="w-10 h-10 p-0.5 border border-gray-300 rounded cursor-pointer" />
                    <input type="text" id="cta_button_text_color_hex" value="#ffffff" maxlength="20"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
        </div>

        <div class="mb-4">
            <x-form-input label="Sort Order" name="sort_order" id="cta_sort_order" type="number" value="0" />
        </div>
        <div class="mb-4">
            <x-form-select label="Status" name="status" id="cta_status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </x-form-select>
        </div>
    </x-entity-crud>

    @push('scripts')
    <script>
        // ===== CTA form fill =====
        window.fillCtaForm = function(data) {
            $('#cta_title').val(data.title);
            $('#cta_subtitle').val(data.subtitle || '');
            $('#cta_description').val(data.description || '');
            $('#cta_button_text').val(data.button_text || '');
            $('#cta_button_link').val(data.button_link || '');
            $('#cta_background_color').val(data.background_color || '#f8f9fa');
            $('#cta_background_color_hex').val(data.background_color || '#f8f9fa');
            $('#cta_text_color').val(data.text_color || '#1f2937');
            $('#cta_text_color_hex').val(data.text_color || '#1f2937');
            $('#cta_button_color').val(data.button_color || '#1e3a8a');
            $('#cta_button_color_hex').val(data.button_color || '#1e3a8a');
            $('#cta_button_text_color').val(data.button_text_color || '#ffffff');
            $('#cta_button_text_color_hex').val(data.button_text_color || '#ffffff');
            $('#cta_sort_order').val(data.sort_order || 0);
            $('#cta_status').val(data.status);

            // Show image preview if available
            if (data.image_url) {
                $('#ctaImagePreview img').attr('src', data.image_url);
                $('#ctaImagePreview').removeClass('hidden');
            } else {
                $('#ctaImagePreview').addClass('hidden');
            }
        };

        $(document).ready(function() {
            // Sync color picker with hex input
            function syncColorPicker(pickerId, hexId) {
                $('#' + pickerId).on('input', function() {
                    $('#' + hexId).val(this.value);
                });
                $('#' + hexId).on('input', function() {
                    if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
                        $('#' + pickerId).val(this.value);
                    }
                });
            }
            syncColorPicker('cta_background_color', 'cta_background_color_hex');
            syncColorPicker('cta_text_color', 'cta_text_color_hex');
            syncColorPicker('cta_button_color', 'cta_button_color_hex');
            syncColorPicker('cta_button_text_color', 'cta_button_text_color_hex');

            // Preview image on file select
            $('#cta_image').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#ctaImagePreview img').attr('src', e.target.result);
                        $('#ctaImagePreview').removeClass('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>