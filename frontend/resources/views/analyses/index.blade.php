{{-- resources/views/analyses/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laboratory Analyses')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Laboratory Analyses</h2>
    <a href="{{ route('analyses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Add Analysis
    </a>
</div>

{{-- Search and Filter Form --}}
<form method="GET" action="{{ route('analyses.index') }}" class="mb-6 bg-white p-4 rounded-lg shadow">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" 
                   placeholder="Search by name, code, category..." 
                   class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category_analyse_id" class="...">  
                <option value="">All Categories</option>  
                @foreach($categories as $category)  
                    <option value="{{ $category['id'] }}" {{ request('category_analyse_id') == $category['id'] ? 'selected' : '' }}>  
                        {{ $category['name'] }}  
                    </option>  
                @endforeach  
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Search
            </button>
        </div>
    </div>
</form>

{{-- Results --}}
@if(empty($analyses))
    <div class="text-center text-gray-600 bg-white p-8 rounded-lg shadow">
        <div class="mb-4">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No analyses found</h3>
        <p class="text-gray-500 mb-4">Get started by creating your first laboratory analysis.</p>
        <a href="{{ route('analyses.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            Create Your First Analysis
        </a>
    </div>
@else
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sample</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($analyses as $analysis)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $analysis['code'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $analysis['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($analysis['category_analyse'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $analysis['category_analyse']['name']  ?? 'N/A' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                {{ number_format($analysis['price'], 2) }} DZD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $analysis['unit']['name'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $analysis['sample_type']['name'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($analysis['is_active'] ?? true)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('analyses.show', $analysis['id']) }}" 
                                       class="text-blue-600 hover:text-blue-900">View</a>
                                    <a href="{{ route('analyses.edit', $analysis['id']) }}" 
                                       class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                    <form action="{{ route('analyses.destroy', $analysis['id']) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this analysis?');" 
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection