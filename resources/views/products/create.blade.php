@extends('layouts.sidebar')

@section('header', 'New Product')
@section('subheader', 'Create a new product and variants')

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
        <form action="{{ route('products.store') }}" method="POST" class="neumorphic p-8 space-y-6" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Product Name</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="name" class="form-input" placeholder="e.g. Product Name" value="{{ old('name') }}" required>
                    </div>
                </div>
            </div>

            <!-- Prices -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

              <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Buying Price (HPP)</label>
                    <div class="neumorphic-inset">
                        <input type="number" name="price_buy" class="form-input" placeholder="0" value="{{ old('price_buy') }}" required>
                    </div>
                </div>

                <!-- Selling Price -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Selling Price</label>
                    <div class="neumorphic-inset">
                        <input type="number" name="price" class="form-input" placeholder="0" value="{{ old('price') }}" required>
                    </div>
                </div>

                <!-- Buying Price -->
              
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Description</label>
                <div class="neumorphic-inset">
                    <textarea name="description" class="form-input" rows="3" placeholder="Product details...">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Image -->
            <div>
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Product Image</label>
                <div class="neumorphic-inset">
                    <input type="file" name="image" class="form-input" accept="image/*">
                </div>
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
                    <!-- Initial Variant Row -->
                    <div class="variant-row flex gap-4 items-start" data-index="0">
                        <div class="flex-1">
                            <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Color</label>
                            <div class="neumorphic-inset">
                                <input type="text" name="variants[0][color]" class="form-input" placeholder="e.g. Black" required>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Size</label>
                            <div class="neumorphic-inset">
                                <input type="text" name="variants[0][size]" class="form-input" placeholder="e.g. 42" required>
                            </div>
                        </div>
                        <div class="pt-6">
                             <button type="button" class="text-red-500 font-bold hover:text-red-700" style="visibility: hidden">X</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="neumorphic-btn w-full py-3 text-blue-600 font-bold hover:text-blue-700">
                    Create Product
                </button>
            </div>
        </form>
    </div>

    <script>
        let variantIndex = 1;

        function addVariant() {
            const container = document.getElementById('variants-container');
            const newRow = document.createElement('div');
            newRow.className = 'variant-row flex gap-4 items-start';
            newRow.dataset.index = variantIndex;
            
            newRow.innerHTML = `
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
                     <button type="button" onclick="removeVariant(this)" class="text-red-500 font-bold hover:text-red-700">X</button>
                </div>
            `;
            
            container.appendChild(newRow);
            variantIndex++;
        }

        function removeVariant(btn) {
            btn.closest('.variant-row').remove();
        }
    </script>
@endsection
