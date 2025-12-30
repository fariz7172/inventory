<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $movement->reference_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
                background-color: white !important;
            }
            .no-print { display: none; }
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    
    <div class="max-w-4xl mx-auto bg-white p-10 shadow-lg print:shadow-none print:p-0">
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-gray-800 pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    {{ $movement->type === 'inbound' ? 'SERAH TERIMA BARANG' : 'SURAT JALAN' }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $movement->type === 'inbound' ? 'Goods Receipt / Masuk Gudang' : 'Delivery Order / Transfer Stock' }}
                </p>
                <div class="mt-4">
                    <p class="font-bold text-gray-800">SHOES INVENTORY SYSTEM</p>
                    <p class="text-sm text-gray-600">Jl. Contoh No. 123, Jakarta</p>
                    <p class="text-sm text-gray-600">Telp: (021) 1234-5678</p>
                </div>
            </div>
            <div class="text-right">
                <div class="mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase">Reference Code</p>
                    <p class="text-xl font-mono font-bold">{{ $movement->reference_code ?? 'N/A' }}</p>
                </div>
                <div class="mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase">Date</p>
                    <p class="font-bold">{{ $movement->created_at->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Type</p>
                    <p class="font-bold uppercase">{{ $movement->type }}</p>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <!-- Left Box -->
            <div class="border p-4 rounded-lg bg-gray-50 print:bg-white print:border-gray-300">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-2 border-b pb-1">
                    {{ $movement->type === 'inbound' ? 'From (Supplier / Source)' : 'From (Origin)' }}
                </h3>
                @if($movement->type === 'inbound')
                    <p class="font-bold text-lg text-gray-800">Supplier / External</p>
                    <p class="text-sm text-gray-600">Vendor</p>
                @else
                    <p class="font-bold text-lg text-gray-800">{{ $movement->rak->warehouse->name }}</p>
                    <p class="text-sm text-gray-600">{{ $movement->rak->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $movement->rak->location_code }}</p>
                @endif
            </div>
            
            <!-- Right Box -->
            <div class="border p-4 rounded-lg bg-gray-50 print:bg-white print:border-gray-300">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-2 border-b pb-1">
                    {{ $movement->type === 'inbound' ? 'Received At (Warehouse)' : 'To (Destination)' }}
                </h3>
                @if($movement->type === 'inbound')
                    <p class="font-bold text-lg text-gray-800">{{ $movement->rak->warehouse->name }}</p>
                    <p class="text-sm text-gray-600">{{ $movement->rak->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $movement->rak->location_code }}</p>
                @elseif($movement->toRak)
                    <p class="font-bold text-lg text-gray-800">{{ $movement->toRak->warehouse->name }}</p>
                    <p class="text-sm text-gray-600">{{ $movement->toRak->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $movement->toRak->location_code }}</p>
                @else
                    <p class="font-bold text-lg text-gray-800">External / Customer</p>
                    <p class="text-sm text-gray-600">N/A</p>
                @endif
            </div>
        </div>

        @if($movement->description)
            <div class="mb-8 p-3 bg-gray-50 border-l-4 border-gray-500 print:bg-white print:border-gray-300">
                <p class="text-sm text-gray-600"><span class="font-bold">Note:</span> {{ $movement->description }}</p>
            </div>
        @endif

        <!-- Items Table -->
        <div class="mb-8">
            <h3 class="font-bold text-lg text-gray-800 mb-4">Item Details</h3>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 print:bg-gray-200">
                        <th class="border border-gray-300 p-3 text-left w-12">#</th>
                        <th class="border border-gray-300 p-3 text-left">Product Name</th>
                        <th class="border border-gray-300 p-3 text-left">Variant</th>
                        <th class="border border-gray-300 p-3 text-center w-24">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 p-3 text-center">1</td>
                        <td class="border border-gray-300 p-3 font-bold">{{ $movement->variant->product->name }}</td>
                        <td class="border border-gray-300 p-3">{{ $movement->variant->color }} - {{ $movement->variant->size }}</td>
                        <td class="border border-gray-300 p-3 text-center font-bold">{{ $movement->quantity }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Serial Numbers List -->
        @php
            $items = $movement->type === 'inbound' ? $movement->inboundItems : $movement->outboundItems;
        @endphp

        @if($items->count() > 0)
            <div class="mb-12">
                 <h3 class="font-bold text-sm text-gray-600 mb-2 uppercase">Serial Numbers / Item Codes</h3>
                 <div class="border rounded p-4 bg-gray-50 print:bg-white print:border-gray-300">
                     <div class="grid grid-cols-3 gap-2 font-mono text-xs">
                        @foreach($items as $index => $item)
                            <div class="flex items-center">
                                <span class="text-gray-400 mr-2">{{ $index + 1 }}.</span>
                                <span class="font-bold text-gray-800">{{ $item->serial_number }}</span>
                            </div>
                        @endforeach
                     </div>
                 </div>
            </div>
        @endif

        <!-- Signatures -->
        <div class="grid grid-cols-3 gap-8 mt-20 page-break-inside-avoid">
            <div class="text-center">
                <p class="text-sm font-bold text-gray-600 mb-16">Pengirim (Sender)</p>
                <div class="border-t border-gray-400 w-2/3 mx-auto pt-2">
                    <p class="font-bold">{{ $movement->user->name }}</p>
                </div>
            </div>
            
             <div class="text-center">
                <p class="text-sm font-bold text-gray-600 mb-16">Kurir / Driver</p>
                <div class="border-t border-gray-400 w-2/3 mx-auto pt-2">
                    <p class="text-gray-400">( .......................... )</p>
                </div>
            </div>

             <div class="text-center">
                <p class="text-sm font-bold text-gray-600 mb-16">Penerima (Receiver)</p>
                <div class="border-t border-gray-400 w-2/3 mx-auto pt-2">
                    <p class="text-gray-400">( .......................... )</p>
                </div>
            </div>
        </div>
        
        <!-- Print Button (Hidden when printing) -->
        <div class="mt-8 text-center no-print">
            <button onclick="window.print()" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-lg shadow hover:bg-blue-700 transition">
                Print Document
            </button>
            <a href="{{ url()->previous() }}" class="ml-4 text-gray-600 hover:text-gray-800 hover:underline">Back</a>
        </div>
    </div>

</body>
</html>
