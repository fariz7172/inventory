@extends('layouts.sidebar')

@section('header', 'Edit Warehouse')
@section('subheader', 'Update warehouse and rack details')

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST" class="neumorphic p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Warehouse Name</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="name" class="form-input" value="{{ old('name', $warehouse->name) }}" required>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Location</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="location" class="form-input" value="{{ old('location', $warehouse->location) }}" required>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Type</label>
                    <div class="neumorphic-inset">
                        <select name="status" class="form-input" required>
                            <option value="1" {{ old('status', $warehouse->status) == 1 ? 'selected' : '' }}>Warehouse (Gudang)</option>
                            <option value="2" {{ old('status', $warehouse->status) == 2 ? 'selected' : '' }}>Outlet (Toko)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Raks Section -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="text-lg font-bold text-gray-600">Shelves / Racks</label>
                    <button type="button" onclick="addRak()" class="neumorphic-btn px-4 py-1 text-sm text-green-600 font-bold hover:text-green-700">
                        + Add Rack
                    </button>
                </div>

                <div id="raks-container" class="space-y-6">
                    @foreach($warehouse->raks as $index => $rak)
                        <div class="rak-row border border-gray-200 p-4 rounded-xl relative" id="rak-row-{{ $index }}">
                            <input type="hidden" name="raks[{{ $index }}][id]" value="{{ $rak->id }}">
                            <input type="hidden" name="raks[{{ $index }}][_delete]" value="0" class="delete-flag">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Rack Name</label>
                                    <div class="neumorphic-inset">
                                        <input type="text" name="raks[{{ $index }}][name]" class="form-input" value="{{ $rak->name }}" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Location Code</label>
                                    <div class="neumorphic-inset">
                                        <input type="text" name="raks[{{ $index }}][location_code]" class="form-input" value="{{ $rak->location_code }}" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Detail Location (Optional)</label>
                                    <div class="neumorphic-inset">
                                        <input type="text" name="raks[{{ $index }}][location]" class="form-input" value="{{ $rak->location }}" placeholder="e.g. Floor 2">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Description (Optional)</label>
                                    <div class="neumorphic-inset">
                                        <input type="text" name="raks[{{ $index }}][description]" class="form-input" value="{{ $rak->description }}" placeholder="e.g. For Men Shoes">
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="markForDelete('{{ $index }}')" class="absolute top-2 right-2 text-red-500 font-bold hover:text-red-700 text-xs text-right">Delete</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="neumorphic-btn w-full py-3 text-green-600 font-bold hover:text-green-700">
                    Update Warehouse
                </button>
            </div>
        </form>
    </div>

    <script>
        let rakIndex = {{ count($warehouse->raks) }};

        function addRak() {
            const container = document.getElementById('raks-container');
            const newRow = document.createElement('div');
            newRow.className = 'rak-row border border-gray-200 p-4 rounded-xl relative mt-4';
            newRow.id = `rak-row-${rakIndex}`;
            
            newRow.innerHTML = `
                <input type="hidden" name="raks[${rakIndex}][id]" value="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="neumorphic-inset">
                            <input type="text" name="raks[${rakIndex}][name]" class="form-input" placeholder="Rack Name" required>
                        </div>
                    </div>
                    <div>
                        <div class="neumorphic-inset">
                            <input type="text" name="raks[${rakIndex}][location_code]" class="form-input" placeholder="Loc Code" required>
                        </div>
                    </div>
                    <div>
                        <div class="neumorphic-inset">
                            <input type="text" name="raks[${rakIndex}][location]" class="form-input" placeholder="Detail Location">
                        </div>
                    </div>
                    <div>
                        <div class="neumorphic-inset">
                            <input type="text" name="raks[${rakIndex}][description]" class="form-input" placeholder="Description">
                        </div>
                    </div>
                </div>
                <button type="button" onclick="removeNewRak(this)" class="absolute top-2 right-2 text-red-500 font-bold hover:text-red-700 text-xs">Remove</button>
            `;
            
            container.appendChild(newRow);
            rakIndex++;
        }

        function removeNewRak(btn) {
            btn.closest('.rak-row').remove();
        }

        function markForDelete(index) {
            const row = document.getElementById(`rak-row-${index}`);
            const deleteInput = row.querySelector('.delete-flag');
            deleteInput.value = "1";
            row.style.display = 'none';
        }
    </script>
@endsection
