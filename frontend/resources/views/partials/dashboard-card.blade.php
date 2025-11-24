<a href="{{ $route }}"
   class="block bg-white shadow rounded-xl p-6 border-l-4 border-{{ $color }}-500 hover:shadow-lg transition duration-300 relative"
   id="card-{{ Str::slug($title) }}"
   data-type="{{ $updatedId }}">
    <div class="flex items-center justify-between">
        <div>
            <h4 class="text-md font-semibold text-gray-700">{{ $title }}</h4>
            <p class="text-3xl font-extrabold text-{{ $color }}-600 mt-1" id="{{ $updatedId }}-count">
                {{ $count }}
            </p>
        </div>
        <div class="text-4xl text-{{ $color }}-400">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
    </div>
    <p class="text-xs text-gray-400 mt-3 flex items-center space-x-1" id="{{ $updatedId }}">
        <i class="fas fa-sync-alt animate-spin hidden" id="{{ $updatedId }}-spinner"></i>
        <span>Actualisé récemment</span>
    </p>
</a>
