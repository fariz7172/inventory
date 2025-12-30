@extends('layouts.sidebar')

@section('header', 'Edit Product')
@section('subheader', 'Update product details and variants')

@section('content')
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 font-semibold max-w-3xl">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200 font-semibold max-w-3xl">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200 max-w-3xl">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-3xl">
        <form action="{{ route('products.update', $product->id) }}" method="POST" class="neumorphic p-8 space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Product Name</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="name" class="form-input" value="{{ old('name', $product->name) }}" required>
                    </div>
                </div>
            </div>

            <!-- Prices -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Selling Price -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Selling Price</label>
                    <div class="neumorphic-inset">
                        <input type="number" name="price" class="form-input" value="{{ old('price', $product->price_sell) }}" required>
                    </div>
                </div>

                <!-- Buying Price -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Buying Price (HPP)</label>
                    <div class="neumorphic-inset">
                        <input type="number" name="price_buy" class="form-input" value="{{ old('price_buy', $product->price_buy) }}" required>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Description</label>
                <div class="neumorphic-inset">
                    <textarea name="description" class="form-input" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <!-- Image -->
            <div>
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Product Image</label>
                @if($product->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="Current Image" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                    </div>
                @endif
                <div class="neumorphic-inset">
                    <input type="file" name="image" class="form-input" accept="image/*">
                </div>
                <p class="text-xs text-gray-500 mt-1 ml-1">Leave empty to keep current image</p>
            </div>

            <!-- Variants Section -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="text-lg font-bold text-gray-600">Variants (Color / Size)</label>
                    <button type="button" onclick="addVariant()" class="neumorphic-btn px-4 py-1 text-sm text-blue-600 font-bold hover:text-blue-700">
                        + Add Variant
                    </button>
                </div>

                <div id="variants-container" class="space-y-4">
                    @foreach($product->variants as $index => $variant)
                        <div class="variant-row flex gap-4 items-start" id="variant-row-{{ $index }}">
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                            <input type="hidden" name="variants[{{ $index }}][_delete]" value="0" class="delete-flag">
                            
                            <div class="flex-1">
                                <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Color</label>
                                <div class="neumorphic-inset">
                                    <input type="text" name="variants[{{ $index }}][color]" class="form-input" value="{{ $variant->color }}" required>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Size</label>
                                <div class="neumorphic-inset">
                                    <input type="text" name="variants[{{ $index }}][size]" class="form-input" value="{{ $variant->size }}" required>
                                </div>
                            </div>
                            <div class="pt-6">
                                 <button type="button" onclick="markForDelete('{{ $index }}')" class="text-red-500 font-bold hover:text-red-700">Delete</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="neumorphic-btn w-full py-3 text-blue-600 font-bold hover:text-blue-700">
                    Update Product
                </button>
            </div>
        </form>
    </div>

    <script>
        let variantIndex = {{ count($product->variants) }};

        function addVariant() {
            const container = document.getElementById('variants-container');
            const newRow = document.createElement('div');
            newRow.className = 'variant-row flex gap-4 items-start';
            newRow.id = `variant-row-${variantIndex}`;
            
            newRow.innerHTML = `
                <input type="hidden" name="variants[${variantIndex}][id]" value="">
                <div class="flex-1">
                    <div class="neumorphic-inset">
                        <input type="text" name="variants[${variantIndex}][color]" class="form-input" placeholder="Color" required>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="neumorphic-inset">
                        <input type="text" name="variants[${variantIndex}][size]" class="form-input" placeholder="Size" required>
                    </div>
                </div>
                <div class="pt-3">
                     <button type="button" onclick="removeNewVariant(this)" class="text-red-500 font-bold hover:text-red-700">X</button>
                </div>
            `;
            
            container.appendChild(newRow);
            variantIndex++;
        }

        function removeNewVariant(btn) {
            btn.closest('.variant-row').remove();
        }

        function markForDelete(index) {
            const row = document.getElementById(`variant-row-${index}`);
            const deleteInput = row.querySelector('.delete-flag');
            deleteInput.value = "1";
            row.style.display = 'none';
        }
    </script>
@endsection
