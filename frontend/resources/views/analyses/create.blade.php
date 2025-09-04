{{-- resources/views/analyses/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add New Analysis')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Add New Analysis</h2>
        <p class="text-gray-600">Create a new laboratory analysis with pricing and specifications.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('analyses.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Basic Information --}}
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                        <input type="text" name="code" value="{{ old('code') }}" 
                               class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="e.g., CBC001">
                        @error('code')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" 
                               required>
                        @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    {{-- Category with Create New --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <div class="flex gap-2">
                            <select name="category_analyse_id" id="category_analyse_id" class="flex-1 p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category['id'] }}" {{ old('category_analyse_id') == $category['id'] ? 'selected' : '' }}>
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openCategoryModal()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
                                + New
                            </button>
                        </div>
                        @error('category_analyse_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    {{-- Price --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">DA</span>
                            <input type="number" name="price" value="{{ old('price') }}" 
                                step="0.01" min="0" 
                                class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" 
                                required>
                        </div>
                        @error('price')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>


                    {{-- Unit with Create New --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <div class="flex gap-2">
                            <select name="unit_id" id="unit_id" class="flex-1 p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit['id'] }}" {{ old('unit_id') == $unit['id'] ? 'selected' : '' }}>
                                        {{ $unit['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openUnitModal()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
                                + New
                            </button>
                        </div>
                        @error('unit_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Specifications</h3>
              {{-- Normal Ranges --}}
<div class="border-b border-gray-200 pb-6">
    <div id="normalRangesContainer" class="space-y-6"></div>
    
    <button type="button" onclick="addNormalRange()" 
        class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
        + Add Range
    </button>
</div>

<script>
let rangeCounter = 0;

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('normalRangesContainer');
    // initialize counter from existing server-rendered items
    rangeCounter = container.querySelectorAll('.normal-range-item').length;

    // attach sex->pregnancy toggles for any existing items
    container.querySelectorAll('.normal-range-item').forEach(item => {
        const sexSelect = item.querySelector('.sex-select');
        const pregWrapper = item.querySelector('.pregnancy-wrapper');
        if (!sexSelect || !pregWrapper) return;

        // set initial visibility
        if (sexSelect.value === 'F') pregWrapper.classList.remove('hidden');
        else pregWrapper.classList.add('hidden');

        sexSelect.addEventListener('change', () => {
            if (sexSelect.value === 'F') {
                pregWrapper.classList.remove('hidden');
            } else {
                pregWrapper.classList.add('hidden');
                const chk = pregWrapper.querySelector('input[type="checkbox"]');
                if (chk) chk.checked = false;
            }
        });
    });
});

function addNormalRange(data = {}) {
    const container = document.getElementById('normalRangesContainer');
    const index = rangeCounter++;

    const wrapper = document.createElement('div');
    wrapper.className = 'normal-range-item border p-4 rounded bg-gray-50 space-y-4';

    wrapper.innerHTML = `
        <div class="grid grid-cols-2 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium">Sex</label>
                <select name="normal_ranges[${index}][sex_applicable]" class="w-full p-2 border rounded sex-select">
                    <option value="All" ${data.sex_applicable === 'All' ? 'selected' : ''}>All</option>
                    <option value="M" ${data.sex_applicable === 'M' ? 'selected' : ''}>Male</option>
                    <option value="F" ${data.sex_applicable === 'F' ? 'selected' : ''}>Female</option>
                </select>
            </div>
            <div class="pregnancy-wrapper ${data.sex_applicable === 'F' ? '' : 'hidden'}">
                <label class="block text-sm font-medium">Pregnant</label>
                <input type="checkbox" name="normal_ranges[${index}][pregnant_applicable]" value="1" ${data.pregnant_applicable ? 'checked' : ''} class="h-5 w-5 mt-2">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Age Min</label>
                <input type="number" name="normal_ranges[${index}][age_min]" value="${data.age_min ?? ''}" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-medium">Age Max</label>
                <input type="number" name="normal_ranges[${index}][age_max]" value="${data.age_max ?? ''}" class="w-full p-2 border rounded">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Normal Min</label>
                <input type="number" step="0.01" name="normal_ranges[${index}][normal_min]" value="${data.normal_min ?? ''}" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-medium">Normal Max</label>
                <input type="number" step="0.01" name="normal_ranges[${index}][normal_max]" value="${data.normal_max ?? ''}" class="w-full p-2 border rounded">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="button" onclick="if (confirm('Are you sure you want to remove this normal range?')) this.closest('.normal-range-item').remove()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                Remove
            </button>
        </div>
    `;

    container.appendChild(wrapper);

    // attach toggling on the freshly-added element
    const sexSelect = wrapper.querySelector('.sex-select');
    const pregWrapper = wrapper.querySelector('.pregnancy-wrapper');
    if (sexSelect && pregWrapper) {
        sexSelect.addEventListener('change', () => {
            if (sexSelect.value === 'F') {
                pregWrapper.classList.remove('hidden');
            } else {
                pregWrapper.classList.add('hidden');
                const chk = pregWrapper.querySelector('input[type="checkbox"]');
                if (chk) chk.checked = false;
            }
        });
        // ensure initial visibility matches provided data
        if (sexSelect.value === 'F') pregWrapper.classList.remove('hidden');
        else pregWrapper.classList.add('hidden');
    }
}
</script>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    {{-- Sample Type with Create New --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sample Type</label>
                        <div class="flex gap-2">
                            <select name="sample_type_id" id="sample_type_id" class="flex-1 p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Sample Type</option>
                                @foreach($sampleTypes as $sampleType)
                                    <option value="{{ $sampleType['id'] }}" {{ old('sample_type_id') == $sampleType['id'] ? 'selected' : '' }}>
                                        {{ $sampleType['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openSampleTypeModal()" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
                                + New
                            </button>
                        </div>
                        @error('sample_type_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

           

            {{-- Formula --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Formula (Optional)</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calculation Formula</label>
                    <textarea name="formula" rows="3" 
                              class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Enter calculation formula if applicable">{{ old('formula') }}</textarea>
                    @error('formula')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('analyses.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Save Analysis
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Category Modal --}}  
<div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">  
    <div class="flex items-center justify-center min-h-screen p-4">  
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full">  
            <div class="p-6">  
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Category</h3>  
                <form id="categoryForm">  
                    <div class="mb-4">  
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>  
                        <input type="text" id="categoryName" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" required>  
                        <div id="categoryError" class="text-red-500 text-sm"></div> <!-- Error display -->  
                    </div>  
                    <div class="flex justify-end space-x-3">  
                        <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">  
                            Cancel  
                        </button>  
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">  
                            Create  
                        </button>  
                    </div>  
                </form>  
            </div>  
        </div>  
    </div>  
</div>  
  
{{-- Sample Type Modal --}}  
<div id="sampleTypeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">  
    <div class="flex items-center justify-center min-h-screen p-4">  
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full">  
            <div class="p-6">  
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Sample Type</h3>  
                <form id="sampleTypeForm">  
                    <div class="mb-4">  
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sample Type Name</label>  
                        <input type="text" id="sampleTypeName" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" required>  
                        <div id="sampleTypeError" class="text-red-500 text-sm"></div> <!-- Error display -->  
                    </div>  
                    <div class="flex justify-end space-x-3">  
                        <button type="button" onclick="closeSampleTypeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">  
                            Cancel  
                        </button>  
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">  
                            Create  
                        </button>  
                    </div>  
                </form>  
            </div>  
        </div>  
    </div>  
</div>  
  
{{-- Unit Modal --}}  
<div id="unitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">  
    <div class="flex items-center justify-center min-h-screen p-4">  
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full">  
            <div class="p-6">  
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Unit</h3>  
                <form id="unitForm">  
                    <div class="mb-4">  
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Name</label>  
                        <input type="text" id="unitName" class="w-full p-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" required>  
                        <div id="unitError" class="text-red-500 text-sm"></div> <!-- Error display -->  
                    </div>  
                    <div class="flex justify-end space-x-3">  
                        <button type="button" onclick="closeUnitModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">  
                            Cancel  
                        </button>  
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">  
                            Create  
                        </button>  
                    </div>  
                </form>  
            </div>  
        </div>  
    </div>  
</div>

<script>  
// Temporary storage for new items  
let tempCategories = [];  
let tempSampleTypes = [];  
let tempUnits = [];  
let tempIdCounter = -1; // Use negative IDs for temporary items  
  
// Category Modal Functions  
function openCategoryModal() {  
    document.getElementById('categoryModal').classList.remove('hidden');  
}  
  
function closeCategoryModal() {  
    document.getElementById('categoryModal').classList.add('hidden');  
    document.getElementById('categoryName').value = '';  
    document.getElementById('categoryError').textContent = '';  
}  
  
// Sample Type Modal Functions  
function openSampleTypeModal() {  
    document.getElementById('sampleTypeModal').classList.remove('hidden');  
}  
  
function closeSampleTypeModal() {  
    document.getElementById('sampleTypeModal').classList.add('hidden');  
    document.getElementById('sampleTypeName').value = '';  
    document.getElementById('sampleTypeError').textContent = '';  
}  
  
// Unit Modal Functions  
function openUnitModal() {  
    document.getElementById('unitModal').classList.remove('hidden');  
}  
  
function closeUnitModal() {  
    document.getElementById('unitModal').classList.add('hidden');  
    document.getElementById('unitName').value = '';  
    document.getElementById('unitError').textContent = '';  
}  
  
// Form Submissions - Create temporary options  
document.getElementById('categoryForm').addEventListener('submit', function(e) {  
    e.preventDefault();  
    const name = document.getElementById('categoryName').value.trim();  
    const errorDiv = document.getElementById('categoryError');  
      
    if (!name) {  
        errorDiv.textContent = 'Category name is required';  
        return;  
    }  
      
    // Check for duplicates  
    const select = document.getElementById('category_analyse_id');  
    const existingOptions = Array.from(select.options).map(opt => opt.text.toLowerCase());  
    if (existingOptions.includes(name.toLowerCase())) {  
        errorDiv.textContent = 'Category already exists';  
        return;  
    }  
      
    // Create temporary category  
    const tempCategory = {  
        id: tempIdCounter--,  
        name: name,  
        isTemp: true  
    };  
      
    tempCategories.push(tempCategory);  
      
    // Add to select dropdown  
    const option = new Option(name, tempCategory.id, true, true);  
    option.style.fontStyle = 'italic';  
    option.style.color = '#059669'; // Green color to indicate it's new  
    select.add(option);  
      
    closeCategoryModal();  
});  
  
document.getElementById('sampleTypeForm').addEventListener('submit', function(e) {  
    e.preventDefault();  
    const name = document.getElementById('sampleTypeName').value.trim();  
    const errorDiv = document.getElementById('sampleTypeError');  
      
    if (!name) {  
        errorDiv.textContent = 'Sample type name is required';  
        return;  
    }  
      
    // Check for duplicates  
    const select = document.getElementById('sample_type_id');  
    const existingOptions = Array.from(select.options).map(opt => opt.text.toLowerCase());  
    if (existingOptions.includes(name.toLowerCase())) {  
        errorDiv.textContent = 'Sample type already exists';  
        return;  
    }  
      
    // Create temporary sample type  
    const tempSampleType = {  
        id: tempIdCounter--,  
        name: name,  
        isTemp: true  
    };  
      
    tempSampleTypes.push(tempSampleType);  
      
    // Add to select dropdown  
    const option = new Option(name, tempSampleType.id, true, true);  
    option.style.fontStyle = 'italic';  
    option.style.color = '#059669'; // Green color to indicate it's new  
    select.add(option);  
      
    closeSampleTypeModal();  
});  
  
document.getElementById('unitForm').addEventListener('submit', function(e) {  
    e.preventDefault();  
    const name = document.getElementById('unitName').value.trim();  
    const errorDiv = document.getElementById('unitError');  
      
    if (!name) {  
        errorDiv.textContent = 'Unit name is required';  
        return;  
    }  
      
    // Check for duplicates  
    const select = document.getElementById('unit_id');  
    const existingOptions = Array.from(select.options).map(opt => opt.text.toLowerCase());  
    if (existingOptions.includes(name.toLowerCase())) {  
        errorDiv.textContent = 'Unit already exists';  
        return;  
    }  
      
    // Create temporary unit  
    const tempUnit = {  
        id: tempIdCounter--,  
        name: name,  
        isTemp: true  
    };  
      
    tempUnits.push(tempUnit);  
      
    // Add to select dropdown  
    const option = new Option(name, tempUnit.id, true, true);  
    option.style.fontStyle = 'italic';  
    option.style.color = '#059669'; // Green color to indicate it's new  
    select.add(option);  
      
    closeUnitModal();  
});  
  
// Modify main form submission to include temporary items  
document.querySelector('form[action="{{ route('analyses.store') }}"]').addEventListener('submit', function(e) {  
    // Add hidden inputs for temporary items  
    tempCategories.forEach(category => {  
        const input = document.createElement('input');  
        input.type = 'hidden';  
        input.name = 'temp_categories[]';  
        input.value = JSON.stringify(category);  
        this.appendChild(input);  
    });  
      
    tempSampleTypes.forEach(sampleType => {  
        const input = document.createElement('input');  
        input.type = 'hidden';  
        input.name = 'temp_sample_types[]';  
        input.value = JSON.stringify(sampleType);  
        this.appendChild(input);  
    });  
      
    tempUnits.forEach(unit => {  
        const input = document.createElement('input');  
        input.type = 'hidden';  
        input.name = 'temp_units[]';  
        input.value = JSON.stringify(unit);  
        this.appendChild(input);  
    });  
});  
  
// Close modals when clicking outside  
document.addEventListener('click', function(e) {  
    if (e.target.id === 'categoryModal') closeCategoryModal();  
    if (e.target.id === 'sampleTypeModal') closeSampleTypeModal();  
    if (e.target.id === 'unitModal') closeUnitModal();  
});  
</script>
@endsection


