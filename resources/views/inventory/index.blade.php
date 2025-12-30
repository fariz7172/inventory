@extends('layouts.sidebar')

@section('header', 'Inventory Summary')
@section('subheader', 'Current stock levels across all warehouses')

@section('content')
    <!-- Actions -->
    <div class="mb-8 flex gap-4">
        <a href="{{ route('inventory.inbound') }}" class="neumorphic-btn px-6 py-2 text-green-600 font-bold hover:text-green-700">
            + Inbound (Masuk)
        </a>
        <a href="{{ route('inventory.outbound') }}" class="neumorphic-btn px-6 py-2 text-red-600 font-bold hover:text-red-700">
            - Outbound (Keluar)
        </a>
        <a href="{{ route('inventory.history') }}" class="neumorphic-btn px-6 py-2 text-blue-600 font-bold hover:text-blue-700">
            View History
        </a>
    </div>

    <!-- Content -->
    <div class="neumorphic p-6 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="p-4 border-b border-gray-300">Product</th>
                    <th class="p-4 border-b border-gray-300">Variant</th>
                    <th class="p-4 border-b border-gray-300">Ref Code</th>
                    <th class="p-4 border-b border-gray-300">Rack / Location</th>
                    <th class="p-4 border-b border-gray-300 text-right">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr class="hover:bg-gray-100 transition-colors cursor-pointer" onclick="showItems('{{ $stock->rak_id }}', '{{ $stock->variant_id }}', '{{ $stock->variant->product->name }}', '{{ $stock->variant->color }} - {{ $stock->variant->size }}')">
                        <td class="p-4 font-bold">{{ $stock->variant->product->name }}</td>
                        <td class="p-4 text-sm text-gray-500">
                            {{ $stock->variant->color }} - {{ $stock->variant->size }}
                        </td>
                        <td class="p-4 text-sm font-mono text-gray-600">
                             {{ $stock->latest_reference }}
                        </td>
                        <td class="p-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                {{ $stock->rak->name }} ({{ $stock->rak->location_code }})
                            </span>
                            <div class="text-xs text-gray-400 mt-1">{{ $stock->rak->warehouse->name }}</div>
                        </td>
                        <td class="p-4 text-right font-mono font-bold {{ $stock->quantity < 5 ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $stock->quantity }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">No stock data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4 p-4">
            {{ $stocks->links() }}
        </div>
    </div>

    <!-- Modal Item Details -->
    <div id="itemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Product Details</h3>
                <p class="text-sm text-gray-500" id="modalSubtitle">Variant Info</p>
                
                <div class="mt-4 text-left max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date In</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="modalBody">
                            <!-- Items injection here -->
                        </tbody>
                    </table>
                    <p id="noDataMsg" class="text-center text-gray-400 py-4 hidden">No individual serial numbers found (Bulk Stock).</p>
                    <p id="loadingMsg" class="text-center text-blue-500 py-4 hidden">Loading data...</p>
                </div>

                <div class="items-center px-4 py-3">
                    <button id="ok-btn" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showItems(rakId, variantId, productName, variantInfo) {
            // 1. Show Modal & Set Titles
            document.getElementById('itemModal').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = productName;
            document.getElementById('modalSubtitle').innerText = variantInfo;
            
            // 2. Clear previous data & Show Loading
            const tbody = document.getElementById('modalBody');
            tbody.innerHTML = '';
            document.getElementById('noDataMsg').classList.add('hidden');
            document.getElementById('loadingMsg').classList.remove('hidden');

            // 3. Fetch Data via AJAX
            fetch(`/inventory/items/${rakId}/${variantId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loadingMsg').classList.add('hidden');
                    
                    if (data.length === 0) {
                        document.getElementById('noDataMsg').classList.remove('hidden');
                    } else {
                        data.forEach(item => {
                            const date = new Date(item.created_at).toLocaleDateString();
                            const refCode = item.inbound_movement ? item.inbound_movement.reference_code : '-';
                            const row = `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.serial_number}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${refCode}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingMsg').innerText = "Failed to load data.";
                });
        }

        function closeModal() {
            document.getElementById('itemModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('itemModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
@endsection
