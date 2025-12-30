<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::with('raks')->paginate(10);
        return view('warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|integer|in:1,2',
            'raks' => 'required|array|min:1',
            'raks.*.name' => 'required|string',
            'raks.*.location_code' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Create Warehouse
                $warehouse = Warehouse::create([
                    'name' => $request->name,
                    'location' => $request->location,
                    'status' => $request->status,
                ]);

                // 2. Create Raks
                foreach ($request->raks as $rakData) {
                    $warehouse->raks()->create([
                        'name' => $rakData['name'],
                        'location_code' => $rakData['location_code'],
                        'location' => $rakData['location'] ?? null,
                        'description' => $rakData['description'] ?? null,
                    ]);
                }
            });

            return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create warehouse: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        $warehouse->load('raks');
        return view('warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|integer|in:1,2',
            'raks' => 'nullable|array',
            'raks.*.id' => 'nullable|exists:raks,id',
            'raks.*.name' => 'required|string',
            'raks.*.location_code' => 'required|string',
            'raks.*._delete' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($request, $warehouse) {
                // 1. Update Warehouse
                $warehouse->update([
                    'name' => $request->name,
                    'location' => $request->location,
                    'status' => $request->status,
                ]);

                // 2. Sync Raks
                if ($request->has('raks')) {
                    foreach ($request->raks as $rakData) {
                        if (!empty($rakData['_delete']) && !empty($rakData['id'])) {
                            Rak::destroy($rakData['id']);
                        } elseif (!empty($rakData['id'])) {
                            Rak::where('id', $rakData['id'])->update([
                                'name' => $rakData['name'],
                                'location_code' => $rakData['location_code'],
                                'location' => $rakData['location'] ?? null,
                                'description' => $rakData['description'] ?? null,
                            ]);
                        } else {
                            if (empty($rakData['_delete'])) {
                                $warehouse->raks()->create([
                                    'name' => $rakData['name'],
                                    'location_code' => $rakData['location_code'],
                                    'location' => $rakData['location'] ?? null,
                                    'description' => $rakData['description'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            });

            return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update warehouse: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        try {
            $warehouse->delete();
            return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete warehouse. It might be in use.');
        }
    }
}
