<a href="{{ $route }}" class="block">
    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105 duration-300 border-l-8 border-{{ $color }}-500">
        <div class="p-4 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ $title }}</p>
                <p class="text-3xl font-extrabold text-{{ $color }}-700">{{ $count }}</p>
            </div>
            <div class="w-12 h-12 bg-{{ $color }}-100 rounded-full flex items-center justify-center shadow-inner">
                <i class="{{ $icon }} text-{{ $color }}-600 text-2xl"></i>
            </div>
        </div>
        <div class="bg-{{ $color }}-50 text-{{ $color }}-700 text-sm py-2 px-4">
            <span class="font-medium">Updated:</span> <span id="{{ $updatedId }}">Just now</span>
        </div>
    </div>
</a>
