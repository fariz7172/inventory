@extends('layouts.sidebar')

@section('header', 'New Warehouse')
@section('subheader', 'Create warehouse and rack locations')

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('warehouses.store') }}" method="POST" class="neumorphic p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Warehouse Or Outlet Name</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="name" class="form-input" placeholder="e.g. Gudang Pusat" value="{{ old('name') }}" required>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Location</label>
                    <div class="neumorphic-inset">
                        <input type="text" name="location" class="form-input" placeholder="e.g. Jakarta Selatan" value="{{ old('location') }}" required>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Type</label>
                    <div class="neumorphic-inset">
                        <select name="status" class="form-input" required>
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Warehouse (Gudang)</option>
                            <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>Outlet (Toko)</option>
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
                    <!-- Initial Rak Row -->
                    <div class="rak-row border border-gray-200 p-4 rounded-xl relative" data-index="0">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Rack Name</label>
                                <div class="neumorphic-inset">
                                    <input type="text" name="raks[0][name]" class="form-input" placeholder="e.g. Rak A1" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Location Code</label>
                                <div class="neumorphic-inset">
                                    <input type="text" name="raks[0][location_code]" class="form-input" placeholder="e.g. A1-01" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Detail Location (Optional)</label>
                                <div class="neumorphic-inset">
                                    <input type="text" name="raks[0][location]" class="form-input" placeholder="e.g. Floor 2, Aisle B">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 ml-1 text-gray-700">Description (Optional)</label>
                                <div class="neumorphic-inset">
                                    <input type="text" name="raks[0][description]" class="form-input" placeholder="e.g. For Men Shoes">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="absolute top-2 right-2 text-red-500 font-bold hover:text-red-700 text-xs" style="visibility: hidden">Remove</button>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="neumorphic-btn w-full py-3 text-green-600 font-bold hover:text-green-700">
                    Create Warehouse
                </button>
            </div>
        </form>
    </div>

    <script>
        let rakIndex = 1;

        function addRak() {
            const container = document.getElementById('raks-container');
            const newRow = document.createElement('div');
            newRow.className = 'rak-row border border-gray-200 p-4 rounded-xl relative mt-4';
            newRow.dataset.index = rakIndex;
            
            newRow.innerHTML = `
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
                <button type="button" onclick="removeRak(this)" class="absolute top-2 right-2 text-red-500 font-bold hover:text-red-700 text-xs">Remove</button>
            `;
            
            container.appendChild(newRow);
            rakIndex++;
        }

        function removeRak(btn) {
            btn.closest('.rak-row').remove();
        }
    </script>
@endsection
