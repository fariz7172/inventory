@extends('layouts.sidebar')

@section('header', 'Track Item')
@section('subheader', 'Search by Serial Number or Reference Code')

@section('content')
    <!-- Search Form -->
    <div class="max-w-2xl mx-auto mb-10">
        <form action="{{ route('inventory.tracking') }}" method="GET" class="neumorphic p-8">
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2 ml-1 text-gray-700">Serial Number / Barcode / Reference Code</label>
                <div class="neumorphic-inset">
                    <input type="text" name="q" class="form-input" placeholder="e.g. SN-12345678 or PO-17-12-2025-0001" value="{{ $search ?? '' }}" required autofocus>
                </div>
                <p class="text-xs text-gray-500 mt-2 ml-1">Search by item serial number or transaction reference code</p>
            </div>
            <button type="submit" class="neumorphic-btn w-full py-3 text-blue-600 font-bold hover:text-blue-700">
                Search
            </button>
        </form>
    </div>

    {{-- Result: Single Item by Serial Number --}}
    @if(isset($item) && $item)
        <div class="max-w-4xl mx-auto">
            <div class="neumorphic p-8">
                <div class="flex justify-between items-start border-b border-gray-300 pb-6 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $item->variant->product->name }}</h3>
                        <p class="text-gray-500">{{ $item->variant->color }} - {{ $item->variant->size }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-400">Status</div>
                        @php
                            $isOutlet = $item->rak && $item->rak->warehouse->status == 2;
                            $statusLabel = $isOutlet ? 'OUT TO OUTLET' : strtoupper($item->status);
                            $statusClass = $item->status == 'available' && !$isOutlet ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            if ($isOutlet) $statusClass = 'bg-orange-100 text-orange-800';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-bold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Identifier -->
                    <div>
                        <h4 class="font-bold text-gray-600 mb-2">Item ID</h4>
                        <div class="neumorphic-inset p-4 font-mono text-center text-lg font-bold text-blue-800">
                            {{ $item->serial_number }}
                        </div>
                    </div>

                    <!-- Current Location -->
                    <div>
                        <h4 class="font-bold text-gray-600 mb-2">Location</h4>
                        @if($item->rak)
                            <div class="neumorphic p-4 bg-gray-50">
                                <p class="font-bold text-gray-800">{{ $item->rak->name }}</p>
                                <p class="text-sm text-gray-500">{{ $item->rak->warehouse->name }}</p>
                                <p class="text-xs text-blue-500 mt-1">{{ $item->rak->location_code }}</p>
                            </div>
                        @else
                            <div class="p-4 text-gray-400 italic">Not in rack (Sold/Out)</div>
                        @endif
                    </div>
                </div>

                <!-- History -->
                <div class="mt-8">
                    <h4 class="font-bold text-gray-600 mb-4">History</h4>
                    <div class="space-y-4">
                        <!-- Inbound Info -->
                        @if($item->inboundMovement)
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold">In</div>
                                <div>
                                    <p class="text-sm font-bold">Inbound (Masuk)</p>
                                    <p class="text-xs text-gray-500">{{ $item->created_at->format('d M Y H:i') }} by {{ $item->inboundMovement->user->name }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Outbound Info -->
                        @if($item->outboundMovement)
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold">Out</div>
                                <div>
                                    <p class="text-sm font-bold">Outbound (Keluar)</p>
                                    <p class="text-xs text-gray-500">{{ $item->updated_at->format('d M Y H:i') }} by {{ $item->outboundMovement->user->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    {{-- Result: Movement by Reference Code --}}
    @elseif(isset($movement) && $movement)
        <div class="max-w-4xl mx-auto">
            <div class="neumorphic p-8">
                <!-- Movement Header -->
                <div class="flex justify-between items-start border-b border-gray-300 pb-6 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $movement->reference_code }}</h3>
                        <p class="text-gray-500">{{ $movement->variant->product->name }} ({{ $movement->variant->color }} / {{ $movement->variant->size }})</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-400">Type</div>
                        <span class="px-3 py-1 rounded-full text-sm font-bold {{ $movement->type === 'inbound' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ strtoupper($movement->type) }}
                        </span>
                    </div>
                </div>

                <!-- Movement Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="neumorphic-inset p-4 text-center">
                        <div class="text-xs text-gray-500 mb-1">Quantity</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $movement->quantity }}</div>
                    </div>
                    <div class="neumorphic-inset p-4 text-center">
                        <div class="text-xs text-gray-500 mb-1">Location</div>
                        <div class="font-bold text-gray-800">{{ $movement->rak->warehouse->name }}</div>
                        <div class="text-sm text-gray-600">{{ $movement->rak->name }}</div>
                    </div>
                    <div class="neumorphic-inset p-4 text-center">
                        <div class="text-xs text-gray-500 mb-1">Date / User</div>
                        <div class="font-bold text-gray-800">{{ $movement->created_at->format('d M Y H:i') }}</div>
                        <div class="text-sm text-gray-600">{{ $movement->user->name }}</div>
                    </div>
                </div>

                @if($movement->description)
                    <div class="mb-6 p-4 bg-gray-100 rounded-lg">
                        <span class="text-xs text-gray-500">Description:</span>
                        <p class="text-gray-700">{{ $movement->description }}</p>
                    </div>
                @endif

                <!-- Items List -->
                @if($movementItems && $movementItems->count() > 0)
                    <div>
                        <h4 class="font-bold text-gray-600 mb-4">Items ({{ $movementItems->count() }} Serial Numbers)</h4>
                        <div class="neumorphic-inset overflow-hidden rounded-xl">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th class="p-3 text-xs font-bold text-gray-700">#</th>
                                        <th class="p-3 text-xs font-bold text-gray-700">Serial Number</th>
                                        <th class="p-3 text-xs font-bold text-gray-700">Status</th>
                                        <th class="p-3 text-xs font-bold text-gray-700">Current Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movementItems as $index => $mItem)
                                        <tr class="border-t border-gray-200 hover:bg-gray-100">
                                            <td class="p-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                            <td class="p-3">
                                                <a href="{{ route('inventory.tracking', ['q' => $mItem->serial_number]) }}" 
                                                   class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                                    {{ $mItem->serial_number }}
                                                </a>
                                            </td>
                                            <td class="p-3">
                                                @php
                                                    $isOutletItem = $mItem->rak && $mItem->rak->warehouse->status == 2;
                                                    $itemStatusLabel = $isOutletItem ? 'OUT TO OUTLET' : strtoupper($mItem->status);
                                                    $itemStatusClass = $mItem->status === 'available' && !$isOutletItem ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                                    if ($isOutletItem) $itemStatusClass = 'bg-orange-100 text-orange-800';
                                                @endphp
                                                <span class="px-2 py-1 rounded text-xs font-bold {{ $itemStatusClass }}">
                                                    {{ $itemStatusLabel }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-sm text-gray-600">
                                                {{ $mItem->rak ? $mItem->rak->name : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center p-6 text-gray-400">
                        <p>This is a bulk transaction (no individual serial numbers tracked).</p>
                    </div>
                @endif
            </div>
        </div>

    {{-- No Result Found --}}
    @elseif(request('q'))
        <div class="max-w-2xl mx-auto text-center mt-10">
            <div class="neumorphic p-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No result found for "<strong>{{ request('q') }}</strong>"</p>
                <p class="text-gray-400 text-sm mt-2">Try searching with a different serial number or reference code.</p>
            </div>
        </div>
    @endif
@endsection
