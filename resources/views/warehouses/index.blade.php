@extends('layouts.sidebar')

@section('header', 'Warehouses')
@section('subheader', 'Manage warehouse locations and racks')

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

    <div class="mb-8">
        <a href="{{ route('warehouses.create') }}" class="neumorphic-btn px-6 py-2 text-green-600 font-bold hover:text-green-700">
            + New Warehouse
        </a>
    </div>

    <!-- Content -->
    <div class="neumorphic p-6 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="p-4 border-b border-gray-300">Name</th>
                    <th class="p-4 border-b border-gray-300">Type</th>
                    <th class="p-4 border-b border-gray-300">Location</th>
                    <th class="p-4 border-b border-gray-300">Raks (Locations)</th>
                    <th class="p-4 border-b border-gray-300 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warehouses as $warehouse)
                    <tr class="hover:bg-gray-100 transition-colors">
                        <td class="p-4 font-bold">{{ $warehouse->name }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $warehouse->status == 1 ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $warehouse->status == 1 ? 'WAREHOUSE' : 'OUTLET' }}
                            </span>
                        </td>
                        <td class="p-4">{{ $warehouse->location }}</td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($warehouse->raks as $rak)
                                    <span class="text-xs font-semibold px-2 py-1 rounded bg-orange-100 text-orange-800 border border-orange-200">
                                        {{ $rak->name }} ({{ $rak->location_code }})
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="text-blue-600 hover:text-blue-800 font-bold mr-4">Edit</a>
                            <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This might delete associated racks!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">No warehouses found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4 p-4">
            {{ $warehouses->links() }}
        </div>
    </div>
@endsection
