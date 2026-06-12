@props([
    'product' => null,
    'brands' => [],
    'categories' => [],
    'formTitle' => 'Create New Product',
    'submitButton' => 'Save Product',
    'isEdit' => false,
])

<x-app-layout>
    <div class="p-4">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <nav class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('products.index') }}" class="hover:text-blue-600 transition-colors">Products</a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span class="text-gray-800 font-medium">{{ $isEdit ? 'Edit' : 'Create New' }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-800">{{ $formTitle }}</h1>
            </div>
            <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition active:scale-95 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>

        <form id="productForm" class="space-y-6" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="product_id" value="{{ $product?->id ?? '' }}">

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column - Main Details -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-600"></i> Basic Information
                            </h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-form-input label="Product Name" name="name" id="name" placeholder="Enter product name" value="{{ $product?->name ?? '' }}" required />
                                </div>
                                <div>
                                    <x-form-input label="Slug" name="slug" id="slug" placeholder="auto-generated-slug" value="{{ $product?->slug ?? '' }}" required />
                                </div>
                            </div>

                            <div>
                                <x-form-textarea label="Short Description" name="short_description" id="short_description" placeholder="Brief description for listings (max 500 characters)" rows="3" value="{{ $product?->short_description ?? '' }}" />
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Full Description</label>
                                <textarea name="description" id="description" rows="8" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Detailed product description...">{{ $product?->description ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Media & Images Card -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-images text-green-600"></i> Product Images
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <label for="images" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg cursor-pointer transition active:scale-95 shadow-sm">
                                    <i class="fas fa-upload"></i>
                                    Upload Images
                                </label>
                                <span class="text-xs text-gray-400">JPG, PNG, WebP up to 5MB each. You can select multiple files.</span>
                                <input type="file" name="images[]" id="images" accept="image/*" multiple class="hidden">
                            </div>
                            <div id="imagePreviewContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                @if($isEdit && $product?->images)
                                    @foreach($product->images as $image)
                                        <div class="relative group preview-card bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden {{ $image->is_main ? 'ring-2 ring-indigo-500' : '' }}">
                                            <img src="{{ $image->image_url }}" class="h-40 w-full object-cover" />
                                            @if($image->is_main)
                                                <div class="absolute top-1 left-1 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Main</div>
                                            @endif
                                            <button type="button" class="remove-preview absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity shadow-lg" data-image-id="{{ $image->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <div class="p-2 bg-gray-50 border-t border-gray-100 flex items-center justify-center gap-2">
                                                <input type="radio" name="main_image_id" value="{{ $image->id }}" {{ $image->is_main ? 'checked' : '' }} class="w-3.5 h-3.5 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                <span class="text-[10px] font-medium text-gray-500">Main Photo</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SEO Card -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-search text-purple-600"></i> SEO Settings
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <x-form-input label="SEO Title" name="seo_title" id="seo_title" placeholder="Custom SEO title (optional)" value="{{ $product?->seo_title ?? '' }}" />
                            <x-form-textarea label="SEO Description" name="seo_description" id="seo_description" placeholder="Custom meta description (optional)" rows="2" value="{{ $product?->seo_description ?? '' }}" />
                        </div>
                    </div>
                </div>

                <!-- Right Column - Side Panel -->
                <div class="space-y-6">

                    <!-- Organization Card -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-sitemap text-orange-600"></i> Organization
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <x-form-select label="Brand" name="brand_id" id="brand_id" placeholder="Select Brand" value="{{ $product?->brand_id ?? '' }}">
                                    <option value="">None</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $product?->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div>
                                <label for="category_ids" class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
                                <select name="category_ids[]" id="category_ids" multiple class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" size="5">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $isEdit && $product?->categories->contains($category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple</p>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Card -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-cog text-gray-600"></i> Settings
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <x-form-select label="Product Type" name="product_type" id="product_type" placeholder="Select Type" value="{{ $product?->product_type ?? 'physical' }}">
                                    <option value="physical" {{ ($product?->product_type ?? 'physical') === 'physical' ? 'selected' : '' }}>Physical</option>
                                    <option value="digital" {{ $product?->product_type === 'digital' ? 'selected' : '' }}>Digital</option>
                                    <option value="service" {{ $product?->product_type === 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="bundle" {{ $product?->product_type === 'bundle' ? 'selected' : '' }}>Bundle</option>
                                </x-form-select>
                            </div>

                            <div>
                                <x-form-select label="Status" name="status" id="status" placeholder="Select Status" value="{{ $product?->status ?? 'draft' }}">
                                    <option value="draft" {{ ($product?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ $product?->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="archived" {{ $product?->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                </x-form-select>
                            </div>

                            <div>
                                <x-form-select label="Visibility" name="visibility" id="visibility" placeholder="Select Visibility" value="{{ $product?->visibility ?? 'public' }}">
                                    <option value="public" {{ ($product?->visibility ?? 'public') === 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="hidden" {{ $product?->visibility === 'hidden' ? 'selected' : '' }}>Hidden</option>
                                    <option value="private" {{ $product?->visibility === 'private' ? 'selected' : '' }}>Private</option>
                                </x-form-select>
                            </div>

                            <div>
                                <x-form-input label="Published At" name="published_at" id="published_at" type="datetime-local" placeholder="Schedule publication" value="{{ $product?->published_at?->format('Y-m-d\TH:i') ?? '' }}" />
                            </div>
                        </div>
                    </div>

                    <!-- Variants Card -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-code-branch text-cyan-600"></i> Variants
                            </h2>
                            <button type="button" id="btnAddVariant" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                                <i class="fas fa-plus-circle"></i> Add Variant
                            </button>
                        </div>
                        <div class="p-6">
                            <div id="variantsContainer">
                                <p id="noVariantsMsg" class="text-sm text-gray-400 text-center py-4">No variants added yet. Click "Add Variant" to create product variations.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4">
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition active:scale-95">
                    Cancel
                </a>
                <button type="submit" id="saveBtn" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition active:scale-95 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-save"></i>
                    <span id="submitBtnText">{{ $submitButton }}</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        let isSaving = false;
        let variantIndex = {{ $isEdit ? ($product?->variants->count() ?? 0) : 0 }};
        let productDescriptionEditor;
        let deletedImageIds = [];

        ClassicEditor.create(document.querySelector('#description'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', '|',
                'link', 'imageUpload', 'mediaEmbed', 'blockQuote', 'codeBlock', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', 'alignment', '|',
                'undo', 'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
            },
            image: {
                resizeOptions: [
                    { name: 'resizeImage:original', label: 'Original', value: null },
                    { name: 'resizeImage:compact', label: 'Compact', value: '50%' },
                    { name: 'resizeImage:medium', label: 'Medium', value: '75%' },
                    { name: 'resizeImage:large', label: 'Large', value: '100%' }
                ],
                toolbar: [
                    'toggleImageCaption', 'imageTextAlternative', 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side',
                    'resizeImage'
                ]
            },
            link: { addTargetToExternalLinks: true }
        })
        .then(editor => {
            productDescriptionEditor = editor;
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
        });

        function getVariantTemplate(index) {
            return `
                <div class="variant-item bg-gray-50 rounded-lg p-4 mb-3 border border-gray-200 relative">
                    <button type="button" class="remove-variant absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors" data-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                    <input type="hidden" name="variants[${index}][id]" class="variant-id" value="">
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">SKU *</label>
                            <input type="text" name="variants[${index}][sku]" class="variant-sku w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="SKU" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Variant Name *</label>
                            <input type="text" name="variants[${index}][name]" class="variant-name w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Small, Red, 128GB" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label>
                            <input type="number" step="0.0001" min="0" name="variants[${index}][cost_price]" class="variant-cost w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Sale Price *</label>
                            <input type="number" step="0.0001" min="0" name="variants[${index}][sale_price]" class="variant-price w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Compare At</label>
                            <input type="number" step="0.0001" min="0" name="variants[${index}][compare_at_price]" class="variant-compare w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0.00">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Barcode</label>
                            <input type="text" name="variants[${index}][barcode]" class="variant-barcode w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Barcode (optional)">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Weight (grams)</label>
                            <input type="number" min="0" name="variants[${index}][weight_grams]" class="variant-weight w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mt-3">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="variants[${index}][track_inventory]" value="1" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Track Inventory
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="variants[${index}][allow_backorder]" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Allow Backorder
                        </label>
                    </div>
                </div>
            `;
        }

        $('#btnAddVariant').on('click', function() {
            $('#noVariantsMsg').hide();
            $('#variantsContainer').append(getVariantTemplate(variantIndex));
            variantIndex++;
        });

        $(document).on('click', '.remove-variant', function() {
            const $item = $(this).closest('.variant-item');
            const id = $item.find('.variant-id').val();
            if (id) {
                $item.append(`<input type="hidden" name="deleted_variant_ids[]" value="${id}">`);
            }
            $item.fadeOut(300, function() { $(this).remove(); });
        });

        $('#images').on('change', function() {
            const container = $('#imagePreviewContainer');
            const files = this.files;
            const hasExistingMain = container.find('input[name="main_image_id"]:checked').length > 0 || container.find('.main-badge').length > 0;
            
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                const isFirst = i === 0 && !hasExistingMain && container.find('.preview-card').length === 0;
                
                reader.onload = function(e) {
                    const checkedAttr = isFirst ? 'checked' : '';
                    const badgeHtml = isFirst ? '<div class="main-badge absolute top-1 left-1 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Main</div>' : '';
                    const cardClass = isFirst ? 'ring-2 ring-indigo-500' : '';
                    
                    container.append(`
                        <div class="relative group preview-card bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden ${cardClass}">
                            <img src="${e.target.result}" class="h-40 w-full object-cover" />
                            ${badgeHtml}
                            <button type="button" class="remove-preview absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="p-2 bg-gray-50 border-t border-gray-100 flex items-center justify-center gap-2">
                                <input type="radio" name="main_image_id" value="new_${Date.now()}_${i}" ${checkedAttr} class="w-3.5 h-3.5 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                <span class="text-[10px] font-medium text-gray-500 truncate max-w-[100px]">${files[i].name}</span>
                            </div>
                        </div>
                    `);
                    
                    // If this is newly set as main, uncheck all others
                    if (isFirst) {
                        container.find('input[name="main_image_id"]').not(':last').prop('checked', false);
                        container.find('.preview-card').not(':last').removeClass('ring-2 ring-indigo-500').find('.main-badge').remove();
                    }
                };
                reader.readAsDataURL(files[i]);
            }
        });
        
        // When clicking a main photo radio, update the visual
        $(document).on('change', 'input[name="main_image_id"]', function() {
            $('#imagePreviewContainer .preview-card').removeClass('ring-2 ring-indigo-500');
            $('#imagePreviewContainer .main-badge').remove();
            $(this).closest('.preview-card').addClass('ring-2 ring-indigo-500');
            $(this).closest('.preview-card').prepend('<div class="main-badge absolute top-1 left-1 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Main</div>');
        });

        $(document).on('click', '.remove-preview', function(e) {
            e.preventDefault();
            const $card = $(this).closest('.preview-card');
            const imageId = $(this).data('image-id');
            
            if (imageId) {
                deletedImageIds.push(imageId);
            }
            
            $card.fadeOut(300, function() { $(this).remove(); });
        });

        $('#name').on('input', function() {
            if (!$('#product_id').val()) {
                let slug = $(this).val().toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
                $('#slug').val(slug);
            }
        });

        function setButtonState(disabled, text) {
            $('#saveBtn').prop('disabled', disabled).toggleClass('opacity-70 cursor-not-allowed', disabled);
            $('#submitBtnText').text(text);
        }

        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            if (isSaving) return;

            isSaving = true;
            setButtonState(true, 'Saving...');

            let id = $('#product_id').val();
            let url = id ? "{{ route('products.update', ':id') }}".replace(':id', id) : "{{ route('products.store') }}";
            let formData = new FormData(this);
            if (id) {
                formData.append('_method', 'PUT');
            }

            if (deletedImageIds.length > 0) {
                formData.append('deleted_image_ids', JSON.stringify(deletedImageIds));
            }

            if (productDescriptionEditor) {
                formData.set('description', productDescriptionEditor.getData());
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    isSaving = false;
                    const successText = id ? 'Update Product' : 'Save Product';
                    setButtonState(false, successText);

                    if (res.status === 'success') {
                        Toastify({
                            text: res.message || 'Product saved successfully',
                            duration: 3000,
                            gravity: 'bottom',
                            position: 'right',
                            style: { background: 'linear-gradient(135deg, #16a34a, #4ade80)' },
                        }).showToast();
                        setTimeout(() => {
                            window.location.href = "{{ route('products.index') }}";
                        }, 1000);
                    } else {
                        Swal.fire('Error', res.message || 'Something went wrong', 'error');
                    }
                },
                error: function(xhr) {
                    isSaving = false;
                    const errorText = id ? 'Update Product' : 'Save Product';
                    setButtonState(false, errorText);

                    let errorMsg = 'Server error occurred';
                    if (xhr.responseJSON?.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON?.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', html: errorMsg });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
