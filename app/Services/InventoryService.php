<?php

namespace App\Services;

use App\Models\InventoryStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Handle Inbound (Barang Masuk)
     * Supports both bulk quantity and itemized (serial number) tracking.
     */
    public function inbound($variantId, $rakId, $quantity, $userId, $reference = null, $desc = '', $serialNumbers = [])
    {
        return DB::transaction(function () use ($variantId, $rakId, $quantity, $userId, $reference, $desc, $serialNumbers) {
            // 1. Record Movement
            $movement = StockMovement::create([
                'type' => 'inbound',
                'variant_id' => $variantId,
                'rak_id' => $rakId,
                'user_id' => $userId,
                'quantity' => $quantity, // Positive
                'reference_code' => $reference,
                'description' => $desc,
            ]);

            // 2. Handle Itemized Inventory (if serials provided)
            if (!empty($serialNumbers)) {
                if (count($serialNumbers) != $quantity) {
                    throw new Exception("Mismatch: Quantity is {$quantity} but " . count($serialNumbers) . " serial numbers provided.");
                }

                foreach ($serialNumbers as $sn) {
                    // Check duplicate
                    if (\App\Models\InventoryItem::where('serial_number', $sn)->exists()) {
                         throw new Exception("Duplicate Serial Number: {$sn} already exists.");
                    }

                    \App\Models\InventoryItem::create([
                        'variant_id' => $variantId,
                        'rak_id' => $rakId,
                        'serial_number' => $sn,
                        'status' => 'available',
                        'inbound_id' => $movement->id,
                    ]);
                }
            }

            // 3. Update Stock Snapshot (Maintains count for both methods)
            $stock = InventoryStock::where('rak_id', $rakId)
                ->where('variant_id', $variantId)
                ->first();

            if ($stock) {
                $stock->increment('quantity', $quantity);
            } else {
                InventoryStock::create([
                    'rak_id' => $rakId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity
                ]);
            }

            return true;
        });
    }

    /**
     * Handle Outbound (Barang Keluar dari Gudang ke Outlet)
     * Moves stock from warehouse to outlet and records as 'outbound' type.
     */
    public function outboundToOutlet($variantId, $fromRakId, $toRakId, $quantity, $userId, $reference = null, $desc = '', $serialNumbers = [])
    {
        return DB::transaction(function () use ($variantId, $fromRakId, $toRakId, $quantity, $userId, $reference, $desc, $serialNumbers) {
            // 1. Check Source Stock Availability
            $sourceStock = InventoryStock::where('rak_id', $fromRakId)
                ->where('variant_id', $variantId)
                ->first();

            if (!$sourceStock || $sourceStock->quantity < $quantity) {
                throw new Exception("Insufficient stock in source warehouse. Available: " . ($sourceStock ? $sourceStock->quantity : 0));
            }

            // 2. Record Movement as 'outbound' type
            $movement = StockMovement::create([
                'type' => 'outbound',
                'variant_id' => $variantId,
                'rak_id' => $fromRakId,
                'to_rak_id' => $toRakId,
                'user_id' => $userId,
                'quantity' => $quantity,
                'reference_code' => $reference,
                'description' => $desc,
            ]);

            // 3. Handle Itemized Inventory (if serials provided)
            if (!empty($serialNumbers)) {
                if (count($serialNumbers) != $quantity) {
                    throw new Exception("Mismatch: Quantity is {$quantity} but " . count($serialNumbers) . " serial numbers provided.");
                }

                foreach ($serialNumbers as $sn) {
                    $item = \App\Models\InventoryItem::where('serial_number', $sn)
                        ->where('status', 'available')
                        ->where('rak_id', $fromRakId)
                        ->first();
                    
                    if (!$item) {
                        throw new Exception("Item with Serial {$sn} not found in source warehouse or not available.");
                    }
                    if ($item->variant_id != $variantId) {
                        throw new Exception("Item {$sn} does not match the selected Product.");
                    }

                    // Move item to destination (outlet) rack
                    $item->update([
                        'rak_id' => $toRakId,
                        'outbound_id' => $movement->id,
                    ]);
                }
            }

            // 4. Update Source Stock (decrease from warehouse)
            $sourceStock->decrement('quantity', $quantity);

            // 5. Update Destination Stock (increase in outlet)
            $destStock = InventoryStock::where('rak_id', $toRakId)
                ->where('variant_id', $variantId)
                ->first();

            if ($destStock) {
                $destStock->increment('quantity', $quantity);
            } else {
                InventoryStock::create([
                    'rak_id' => $toRakId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity
                ]);
            }

            return true;
        });
    }

    /**
     * Get Current Stock for a Variant (All Racks)
     */
    public function getStockSummary($variantId)
    {
        return InventoryStock::where('variant_id', $variantId)
            ->with(['rak', 'variant.product'])
            ->get();
    }

    /**
     * Handle Transfer (Pindah Stock antar Rak)
     * Move stock from source rack (warehouse) to destination rack (outlet)
     */
    public function transfer($variantId, $fromRakId, $toRakId, $quantity, $userId, $reference = null, $desc = '', $serialNumbers = [])
    {
        return DB::transaction(function () use ($variantId, $fromRakId, $toRakId, $quantity, $userId, $reference, $desc, $serialNumbers) {
            // 1. Check Source Stock Availability
            $sourceStock = InventoryStock::where('rak_id', $fromRakId)
                ->where('variant_id', $variantId)
                ->first();

            if (!$sourceStock || $sourceStock->quantity < $quantity) {
                throw new Exception("Insufficient stock in source rack. Available: " . ($sourceStock ? $sourceStock->quantity : 0));
            }

            // 2. Record Transfer Movement
            $movement = StockMovement::create([
                'type' => 'transfer',
                'variant_id' => $variantId,
                'rak_id' => $fromRakId,
                'to_rak_id' => $toRakId, // New field for destination
                'user_id' => $userId,
                'quantity' => $quantity,
                'reference_code' => $reference,
                'description' => $desc,
            ]);

            // 3. Handle Itemized Inventory (if serials provided)
            if (!empty($serialNumbers)) {
                if (count($serialNumbers) != $quantity) {
                    throw new Exception("Mismatch: Quantity is {$quantity} but " . count($serialNumbers) . " serial numbers provided.");
                }

                foreach ($serialNumbers as $sn) {
                    $item = \App\Models\InventoryItem::where('serial_number', $sn)
                        ->where('status', 'available')
                        ->where('rak_id', $fromRakId)
                        ->first();
                    
                    if (!$item) {
                        throw new Exception("Item with Serial {$sn} not found in source rack or not available.");
                    }
                    if ($item->variant_id != $variantId) {
                        throw new Exception("Item {$sn} does not match the selected Product.");
                    }

                    // Move item to destination rack
                    $item->update([
                        'rak_id' => $toRakId,
                    ]);
                }
            }

            // 4. Update Source Stock (decrease)
            $sourceStock->decrement('quantity', $quantity);

            // 5. Update Destination Stock (increase)
            $destStock = InventoryStock::where('rak_id', $toRakId)
                ->where('variant_id', $variantId)
                ->first();

            if ($destStock) {
                $destStock->increment('quantity', $quantity);
            } else {
                InventoryStock::create([
                    'rak_id' => $toRakId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity
                ]);
            }

            return true;
        });
    }
}
