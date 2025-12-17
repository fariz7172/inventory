@extends('layouts.sidebar')

@section('header', 'Inbound Stock')
@section('subheader', 'Record incoming inventory items')

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
        <form action="{{ route('inventory.storeInbound') }}" method="POST" class="neumorphic p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Variant Selection -->
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

                <!-- Rak Selection -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Location (Rack)</label>
                    <div class="neumorphic-inset">
                        <select name="rak_id" class="form-input" required>
                            <option value="">Select Rack</option>
                            @foreach($raks as $rak)
                                <option value="{{ $rak->id }}" {{ old('rak_id') == $rak->id ? 'selected' : '' }}>
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
                    <input type="number" name="quantity" class="form-input" min="1" placeholder="Amount to add" value="{{ old('quantity') }}" required>
                </div>
            </div>

            <!-- Serial Numbers (Optional) -->
            <div>
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Serial Numbers / Barcodes (Optional)</label>
                <div class="neumorphic-inset">
                    <textarea name="serial_numbers_input" class="form-input" rows="4" placeholder="Scan barcodes or type serial numbers here (one per line). Total lines must match Quantity.">{{ old('serial_numbers_input') }}</textarea>
                </div>
                <p class="text-xs text-gray-500 mt-1 ml-1">Leave empty for bulk non-serialized tracking.</p>
            </div>

            <!-- Metadata -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Reference No.</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="reference_code" class="form-input" placeholder="e.g. PO-12345" value="{{ old('reference_code', $referenceCode ?? '') }}" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Description</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="description" class="form-input" placeholder="Notes..." value="{{ old('description') }}">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="neumorphic-btn w-full py-3 text-green-600 font-bold hover:text-green-700">
                    Submit Inbound
                </button>
            </div>
        </form>
    </div>
@endsection
