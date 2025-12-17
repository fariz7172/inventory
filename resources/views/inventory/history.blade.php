@extends('layouts.sidebar')

@section('header', 'Movement History')
@section('subheader', 'Log of all inbound and outbound transactions')

@section('content')
    <div class="neumorphic p-6 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="p-4 border-b border-gray-300">Date</th>
                    <th class="p-4 border-b border-gray-300">Type</th>
                    <th class="p-4 border-b border-gray-300">Product</th>
                    <th class="p-4 border-b border-gray-300">Location</th>
                    <th class="p-4 border-b border-gray-300">Quantity</th>
                    <th class="p-4 border-b border-gray-300">Ref / User</th>
                    <th class="p-4 border-b border-gray-300">Items</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    @php
                        $items = $movement->type === 'inbound' ? $movement->inboundItems : $movement->outboundItems;
                    @endphp
                    <tr class="hover:bg-gray-100 transition-colors">
                        <td class="p-4 text-sm">{{ $movement->created_at->format('d M Y H:i') }}</td>
                        <td class="p-4">
                            @if($movement->type === 'inbound')
                                <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-800">INBOUND</span>
                            @elseif($movement->type === 'outbound')
                                <span class="px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-800">OUTBOUND</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-bold bg-purple-100 text-purple-800">TRANSFER</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-bold">{{ $movement->variant->product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $movement->variant->color }} / {{ $movement->variant->size }}</div>
                        </td>
                        <td class="p-4 text-sm">
                            @if(($movement->type === 'transfer' || $movement->type === 'outbound') && $movement->toRak)
                                <span class="text-blue-600" title="{{ $movement->rak->warehouse->name }}">
                                    {{ $movement->rak->warehouse->name }} - {{ $movement->rak->name }}
                                </span>
                                <span class="text-gray-400 mx-1">→</span>
                                <span class="{{ $movement->type === 'outbound' ? 'text-red-600' : 'text-purple-600' }}" title="{{ $movement->toRak->warehouse->name }}">
                                    {{ $movement->toRak->warehouse->name }} - {{ $movement->toRak->name }}
                                </span>
                            @else
                                {{ $movement->rak->warehouse->name }} - {{ $movement->rak->name }}
                            @endif
                        </td>
                        <td class="p-4 font-mono font-bold">{{ $movement->quantity }}</td>
                        <td class="p-4 text-sm">
                            <div class="font-bold">{{ $movement->reference_code ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $movement->user->name }}</div>
                        </td>
                        <td class="p-4">
                            @if($items->count() > 0)
                                <button type="button" 
                                    onclick="showItemsModal({{ $movement->id }})" 
                                    class="neumorphic-btn px-3 py-1 text-xs text-blue-600 font-bold hover:text-blue-700">
                                    {{ $items->count() }} Items
                                </button>
                            @else
                                <span class="text-xs text-gray-400">Bulk</span>
                            @endif

                             <a href="{{ route('inventory.print', $movement->id) }}" target="_blank" class="ml-2 text-gray-400 hover:text-gray-600" title="Print Surat Jalan">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Hidden items data for modal -->
                    @if($items->count() > 0)
                        @php
                            $itemsData = $items->map(function($item) use ($movement) {
                                return [
                                    'serial_number' => $item->serial_number,
                                    'status' => $movement->type === 'outbound' ? 'Out To Outlet' : $item->status,
                                    'rak' => $item->rak ? $item->rak->name : 'N/A',
                                    'created_at' => $item->created_at->format('d M Y H:i')
                                ];
                            });
                        @endphp
                        <tr id="items-data-{{ $movement->id }}" class="hidden">
                            <td colspan="7">
                                <div class="items-json" data-items="{{ json_encode($itemsData) }}"></div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">No movement history found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 p-4">
            {{ $movements->links() }}
        </div>
    </div>

    <!-- Items Detail Modal -->
    <div id="items-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeItemsModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl max-h-[80vh] overflow-hidden">
            <div class="neumorphic bg-[#e0e5ec] p-6 rounded-2xl shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Item Details (Serial Numbers)</h3>
                    <button onclick="closeItemsModal()" class="neumorphic-btn p-2 text-gray-600 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="overflow-y-auto max-h-[60vh]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="p-3 border-b border-gray-300 text-sm font-bold text-gray-700">#</th>
                                <th class="p-3 border-b border-gray-300 text-sm font-bold text-gray-700">Serial Number</th>
                                <th class="p-3 border-b border-gray-300 text-sm font-bold text-gray-700">Status</th>
                                <th class="p-3 border-b border-gray-300 text-sm font-bold text-gray-700">Location</th>
                            </tr>
                        </thead>
                        <tbody id="items-table-body">
                            <!-- Items will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showItemsModal(movementId) {
            const dataRow = document.getElementById(`items-data-${movementId}`);
            const itemsJson = dataRow.querySelector('.items-json').dataset.items;
            const items = JSON.parse(itemsJson);
            
            const tbody = document.getElementById('items-table-body');
            tbody.innerHTML = '';
            
            items.forEach((item, index) => {
                const statusClass = item.status === 'available' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800';
                
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-100 transition-colors';
                row.innerHTML = `
                    <td class="p-3 text-sm text-gray-600">${index + 1}</td>
                    <td class="p-3">
                        <span class="font-mono font-bold text-blue-800 bg-blue-50 px-2 py-1 rounded">${item.serial_number}</span>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs font-bold ${statusClass}">
                            ${item.status.toUpperCase()}
                        </span>
                    </td>
                    <td class="p-3 text-sm text-gray-600">${item.rak}</td>
                `;
                tbody.appendChild(row);
            });
            
            document.getElementById('items-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeItemsModal() {
            document.getElementById('items-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeItemsModal();
            }
        });
    </script>
@endsection
