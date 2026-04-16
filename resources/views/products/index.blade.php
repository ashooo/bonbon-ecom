@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-8 lg:flex-row">
        <aside class="w-full lg:w-1/4 lg:pr-8">
            <h2 class="text-2xl font-bold mb-6">Filters</h2>

            <form method="GET" action="{{ url('/products') }}" class="space-y-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Category</h3>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="category" value="" class="mr-2" {{ request('category') ? '' : 'checked' }}>
                            All Categories
                        </label>
                        @foreach($categories as $category)
                            <label class="flex items-center">
                                <input type="radio" name="category" value="{{ $category->id }}" class="mr-2" {{ (string) request('category') === (string) $category->id ? 'checked' : '' }}>
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Sort</h3>
                    <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">Sort by: Name</option>
                        <option value="latest" @selected(request('sort') === 'latest')>Sort by: Latest</option>
                        <option value="price_low" @selected(request('sort') === 'price_low')>Sort by: Price (Low to High)</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>Sort by: Price (High to Low)</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">Apply</button>
                    <a href="{{ url('/products') }}" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Reset</a>
                </div>
            </form>
        </aside>

        <main class="w-full lg:w-3/4">
            <form method="GET" action="{{ url('/products') }}" class="flex flex-col gap-4 mb-6 md:flex-row md:justify-between md:items-center">
                <div class="flex-1 max-w-md">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="ml-0 md:ml-4 flex gap-3 items-center">
                    <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">Sort by: Name</option>
                        <option value="latest" @selected(request('sort') === 'latest')>Sort by: Latest</option>
                        <option value="price_low" @selected(request('sort') === 'price_low')>Sort by: Price (Low to High)</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>Sort by: Price (High to Low)</option>
                    </select>
                    <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">Search</button>
                </div>
            </form>

            <div class="mb-4 text-sm text-gray-600">
                {{ $products->total() }} cakes available
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    @include('components.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full rounded-lg border border-dashed border-[#E6B7BE] bg-white p-12 text-center text-gray-500">
                        No products found.
                    </div>
                @endforelse
            </div>

            @if($products->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif
        </main>
    </div>
@endsection
