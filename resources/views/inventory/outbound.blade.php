@extends('layouts.sidebar')

@section('header', 'Outbound Stock')
@section('subheader', 'Transfer items from Warehouse to Outlet')

@section('content')
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200 font-semibold">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-3xl">
        <form action="{{ route('inventory.storeOutbound') }}" method="POST" class="neumorphic p-8 space-y-6">
            @csrf
            
            <!-- Product Variant -->
            <div>
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Product Variant</label>
                <div class="neumorphic-inset">
                    <select name="variant_id" class="form-input" required>
                        <option value="">Select Variant</option>
                        @foreach($variants as $variant)
                            <option value="{{ $variant->id }}" {{ old('variant_id') == $variant->id ? 'selected' : '' }}>
                                {{ $variant->product->name }} ({{ $variant->color }}/{{ $variant->size }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- From Location (Warehouse) -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">
                        <span class="text-blue-600">From:</span> Warehouse (Gudang)
                    </label>
                    <div class="neumorphic-inset">
                        <select name="from_rak_id" class="form-input" required>
                            <option value="">Select Source Rack</option>
                            @foreach($sourceRaks as $rak)
                                <option value="{{ $rak->id }}" {{ old('from_rak_id') == $rak->id ? 'selected' : '' }}>
                                    {{ $rak->warehouse->name }} - {{ $rak->name }} ({{ $rak->location_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- To Location (Outlet) -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">
                        <span class="text-red-600">To:</span> Outlet (Toko)
                    </label>
                    <div class="neumorphic-inset">
                        <select name="to_rak_id" class="form-input" required>
                            <option value="">Select Destination Rack</option>
                            @foreach($destRaks as $rak)
                                <option value="{{ $rak->id }}" {{ old('to_rak_id') == $rak->id ? 'selected' : '' }}>
                                    {{ $rak->warehouse->name }} - {{ $rak->name }} ({{ $rak->location_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Quantity</label>
                <div class="neumorphic-inset">
                    <input type="number" name="quantity" class="form-input" min="1" placeholder="Amount to transfer" value="{{ old('quantity') }}" required>
                </div>
            </div>

            <!-- Serial Numbers (Optional) -->
            <div>
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Serial Numbers / Barcodes (Optional)</label>
                <div class="neumorphic-inset">
                    <textarea name="serial_numbers_input" class="form-input" rows="4" placeholder="Scan barcodes of items (one per line).">{{ old('serial_numbers_input') }}</textarea>
                </div>
                <p class="text-xs text-gray-500 mt-1 ml-1">Leave empty for bulk non-serialized transfer.</p>
            </div>

            <!-- Metadata -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Reference No.</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="reference_code" class="form-input" placeholder="e.g. INV-001" value="{{ old('reference_code', $referenceCode ?? '') }}" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Description</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="description" class="form-input" placeholder="Reason..." value="{{ old('description') }}">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="neumorphic-btn w-full py-3 text-red-600 font-bold hover:text-red-700">
                    Submit Outbound
                </button>
            </div>
        </form>
    </div>
@endsection
