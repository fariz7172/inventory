<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\Rak;
use App\Models\StockMovement;
use App\Models\Variant;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display current stock inventory
     */
    public function index()
    {
        $stocks = InventoryStock::with(['rak', 'variant.product'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        
        return view('inventory.index', compact('stocks'));
    }

    /**
     * Show form for Inbound (Barang Masuk)
     */
    public function inbound()
    {
        $variants = Variant::with('product')->get();
        
        // Only get raks from warehouses (status = 1)
        $raks = Rak::with('warehouse')
            ->whereHas('warehouse', function($query) {
                $query->where('status', 1); // 1 = Warehouse
            })
            ->get();
        
        // Generate auto reference code: PO-DD-MM-YYYY-XXXX
        $today = now();
        $datePrefix = 'PO-' . $today->format('d-m-Y') . '-';
        
        // Count today's inbound transactions to generate sequence
        $todayCount = StockMovement::where('type', 'inbound')
            ->whereDate('created_at', $today->toDateString())
            ->count();
        
        $sequence = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
        $referenceCode = $datePrefix . $sequence;
        
        return view('inventory.inbound', compact('variants', 'raks', 'referenceCode'));
    }

    /**
     * Process Inbound
     */
    public function storeInbound(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'rak_id' => 'required|exists:raks,id',
            'quantity' => 'required|integer|min:1',
            'reference_code' => 'nullable|string',
            'description' => 'nullable|string',
            'serial_numbers_input' => 'nullable|string', // Textarea input for serials
        ]);

        try {
            $serialNumbers = [];
            if ($request->filled('serial_numbers_input')) {
                // Split by newline or comma
                $rawSerials = preg_split('/[\n\r,]+/', $request->serial_numbers_input);
                $serialNumbers = array_filter(array_map('trim', $rawSerials));
            }

            $this->inventoryService->inbound(
                $request->variant_id,
                $request->rak_id,
                $request->quantity,
                Auth::id(),
                $request->reference_code,
                $request->description,
                $serialNumbers
            );
            return redirect()->route('inventory.index')->with('success', 'Inbound transaction recorded successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Inbound Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show form for Outbound (Barang Keluar) - Gudang to Outlet
     */
    public function outbound()
    {
        $variants = Variant::with('product')->get();
        
        // Source: Warehouse raks (status = 1) - where stock comes from
        $sourceRaks = Rak::with('warehouse')
            ->whereHas('warehouse', function($query) {
                $query->where('status', 1); // 1 = Warehouse
            })
            ->get();
        
        // Destination: Outlet raks (status = 2) - where stock goes to
        $destRaks = Rak::with('warehouse')
            ->whereHas('warehouse', function($query) {
                $query->where('status', 2); // 2 = Outlet
            })
            ->get();
        
        // Generate auto reference code: INV-DD-MM-YYYY-XXXX
        $today = now();
        $datePrefix = 'INV-' . $today->format('d-m-Y') . '-';
        
        // Count today's outbound transactions to generate sequence
        $todayCount = StockMovement::where('type', 'outbound')
            ->whereDate('created_at', $today->toDateString())
            ->count();
        
        $sequence = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
        $referenceCode = $datePrefix . $sequence;
        
        return view('inventory.outbound', compact('variants', 'sourceRaks', 'destRaks', 'referenceCode'));
    }

    /**
     * Process Outbound (Transfer from Warehouse to Outlet)
     */
    public function storeOutbound(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'from_rak_id' => 'required|exists:raks,id',
            'to_rak_id' => 'required|exists:raks,id|different:from_rak_id',
            'quantity' => 'required|integer|min:1',
            'reference_code' => 'nullable|string',
            'description' => 'nullable|string',
            'serial_numbers_input' => 'nullable|string',
        ]);

        try {
            $serialNumbers = [];
            if ($request->filled('serial_numbers_input')) {
                $rawSerials = preg_split('/[\n\r,]+/', $request->serial_numbers_input);
                $serialNumbers = array_filter(array_map('trim', $rawSerials));
            }

            // Use outboundToOutlet service to move from Warehouse to Outlet
            $this->inventoryService->outboundToOutlet(
                $request->variant_id,
                $request->from_rak_id,
                $request->to_rak_id,
                $request->quantity,
                Auth::id(),
                $request->reference_code,
                $request->description ?? 'Outbound to Outlet',
                $serialNumbers
            );
            return redirect()->route('inventory.index')->with('success', 'Outbound transaction recorded successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Outbound Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show form for Transfer (Pindah Barang)
     */
    public function transfer()
    {
        $variants = Variant::with('product')->get();
        
        // Source: only raks from warehouses (status = 1)
        $sourceRaks = Rak::with('warehouse')
            ->whereHas('warehouse', function($query) {
                $query->where('status', 1);
            })
            ->get();
        
        // Destination: only raks from outlets (status = 2)
        $destRaks = Rak::with('warehouse')
            ->whereHas('warehouse', function($query) {
                $query->where('status', 2);
            })
            ->get();
        
        // Generate auto reference code: TRF-DD-MM-YYYY-XXXX
        $today = now();
        $datePrefix = 'TRF-' . $today->format('d-m-Y') . '-';
        
        $todayCount = StockMovement::where('type', 'transfer')
            ->whereDate('created_at', $today->toDateString())
            ->count();
        
        $sequence = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
        $referenceCode = $datePrefix . $sequence;
        
        return view('inventory.transfer', compact('variants', 'sourceRaks', 'destRaks', 'referenceCode'));
    }

    /**
     * Process Transfer
     */
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'from_rak_id' => 'required|exists:raks,id',
            'to_rak_id' => 'required|exists:raks,id|different:from_rak_id',
            'quantity' => 'required|integer|min:1',
            'reference_code' => 'nullable|string',
            'description' => 'nullable|string',
            'serial_numbers_input' => 'nullable|string',
        ]);

        try {
            $serialNumbers = [];
            if ($request->filled('serial_numbers_input')) {
                $rawSerials = preg_split('/[\n\r,]+/', $request->serial_numbers_input);
                $serialNumbers = array_filter(array_map('trim', $rawSerials));
            }

            $this->inventoryService->transfer(
                $request->variant_id,
                $request->from_rak_id,
                $request->to_rak_id,
                $request->quantity,
                Auth::id(),
                $request->reference_code,
                $request->description,
                $serialNumbers
            );
            return redirect()->route('inventory.index')->with('success', 'Transfer completed successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Transfer Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show Movement History
     */
    public function history()
    {
        $movements = StockMovement::with(['rak.warehouse', 'toRak.warehouse', 'variant.product', 'user', 'inboundItems', 'outboundItems'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('inventory.history', compact('movements'));
    }

    /**
     * Search Item by Serial Number or Reference Code
     */
    public function search(Request $request)
    {
        $search = $request->input('q');
        $item = null;
        $movement = null;
        $movementItems = null;

        if ($search) {
            // First try to find by serial number
            $item = \App\Models\InventoryItem::where('serial_number', $search)
                ->with(['variant.product', 'rak.warehouse', 'inboundMovement.user', 'outboundMovement.user'])
                ->first();
            
            // If not found, try to find by reference code
            if (!$item) {
                $movement = StockMovement::where('reference_code', $search)
                    ->with(['variant.product', 'rak.warehouse', 'user', 'inboundItems.rak', 'outboundItems.rak'])
                    ->first();
                
                if ($movement) {
                    // Get items based on movement type
                    $movementItems = $movement->type === 'inbound' 
                        ? $movement->inboundItems 
                        : $movement->outboundItems;
                }
            }
        }

        return view('inventory.tracking', compact('item', 'movement', 'movementItems', 'search'));
    }

    /**
     * Get Items for AJAX Modal
     */
    public function getItems($rakId, $variantId)
    {
        $items = \App\Models\InventoryItem::with('inboundMovement')
            ->where('rak_id', $rakId)
            ->where('variant_id', $variantId)
            ->where('status', 'available')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($items);
    }

    /**
     * Print Surat Jalan (Delivery Order)
     */
    public function print($id)
    {
        $movement = \App\Models\StockMovement::with([
            'variant.product', 
            'rak.warehouse', 
            'toRak.warehouse', 
            'user',
            'inboundItems',
            'outboundItems'
        ])->findOrFail($id);

        return view('inventory.print', compact('movement'));
    }
}
