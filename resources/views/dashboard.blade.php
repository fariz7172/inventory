@extends('layouts.sidebar')

@section('header', 'Dashboard')
@section('subheader', 'Overview of your inventory status')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <!-- Total Products -->
        <div class="neumorphic p-8 flex items-center gap-6">
            <div class="p-4 rounded-full bg-blue-100 text-blue-600neumorphic-inset">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 font-bold text-sm">Total Products</p>
                <p class="text-3xl font-extrabold text-blue-600">{{ $productCount }}</p>
            </div>
        </div>
        
        <!-- Total Stock -->
        <div class="neumorphic p-8 flex items-center gap-6">
            <div class="p-4 rounded-full bg-green-100 text-green-600">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 font-bold text-sm">Total Items in Stock</p>
                <p class="text-3xl font-extrabold text-green-600">{{ $stockCount }}</p>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="neumorphic p-8 flex items-center gap-6">
            <div class="p-4 rounded-full bg-orange-100 text-orange-600">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 font-bold text-sm">Low Stock Alerts</p>
                <p class="text-3xl font-extrabold text-orange-600">{{ $lowStockCount }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Access Actions -->
    <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <a href="{{ route('inventory.inbound') }}" class="neumorphic p-8 hover:bg-gray-100 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                </div>
                <span class="text-blue-600 font-bold">&rarr;</span>
            </div>
            <h4 class="text-lg font-bold text-gray-800">Inbound Stock</h4>
            <p class="text-sm text-gray-500 mt-1">Record incoming goods</p>
        </a>

        <a href="{{ route('inventory.outbound') }}" class="neumorphic p-8 hover:bg-gray-100 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                </div>
                <span class="text-red-600 font-bold">&rarr;</span>
            </div>
            <h4 class="text-lg font-bold text-gray-800">Outbound Stock</h4>
            <p class="text-sm text-gray-500 mt-1">Record outgoing items</p>
        </a>

         <a href="{{ route('products.create') }}" class="neumorphic p-8 hover:bg-gray-100 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
                <span class="text-purple-600 font-bold">&rarr;</span>
            </div>
            <h4 class="text-lg font-bold text-gray-800">Add Product</h4>
            <p class="text-sm text-gray-500 mt-1">Register new SKU</p>
        </a>
    </div>
@endsection
