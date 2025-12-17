@extends('layouts.sidebar')

@section('header', 'Products')
@section('subheader', 'Manage master data for products')

@section('content')
    <div class="mb-8">
        <a href="{{ route('products.create') }}" class="neumorphic-btn px-6 py-2 text-blue-600 font-bold hover:text-blue-700">
            + New Product
        </a>
    </div>

    <!-- Content -->
    <div class="neumorphic p-6 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="p-4 border-b border-gray-300">Image</th>
                    <th class="p-4 border-b border-gray-300">Name</th>
                    <th class="p-4 border-b border-gray-300">Description</th>
                    <th class="p-4 border-b border-gray-300">Buying Price</th>
                    <th class="p-4 border-b border-gray-300">Selling Price</th>
                    <th class="p-4 border-b border-gray-300">Variants</th>
                    <th class="p-4 border-b border-gray-300 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="hover:bg-gray-100 transition-colors">
                        <td class="p-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="w-16 h-16 object-cover rounded shadow">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">No Img</div>
                            @endif
                        </td>
                        <td class="p-4 font-bold">{{ $product->name }}</td>
                        <td class="p-4 text-sm">{{ Str::limit($product->description, 50) }}</td>
                        <td class="p-4 text-gray-600">Rp {{ number_format($product->price_buy, 0, ',', '.') }}</td>
                        <td class="p-4 text-green-600 font-bold">Rp {{ number_format($product->price_sell, 0, ',', '.') }}</td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->variants as $variant)
                                    <span class="text-xs font-semibold px-2 py-1 rounded bg-gray-200">
                                        {{ $variant->color }} / {{ $variant->size }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800 font-bold mr-4">Edit</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4 p-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
