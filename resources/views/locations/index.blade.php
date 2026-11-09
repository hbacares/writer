<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Locations: {{ $story->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('stories.locations.create', $story) }}" class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">New Location</a>
                <a href="{{ route('stories.show', $story) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">Back</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if ($locations->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center text-gray-600 dark:text-gray-400">No locations yet. Create your first location!</div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($locations as $location)
                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                            @if ($location->image)
                                <img src="{{ asset('storage/' . $location->image) }}" alt="{{ $location->name }}" class="w-full h-48 object-cover">
                            @endif
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $location->name }}</h3>
                                @if ($location->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($location->description, 150) }}</p>
                                @endif
                                <div class="flex gap-2">
                                    <a href="{{ route('locations.show', $location) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View</a>
                                    <a href="{{ route('locations.edit', $location) }}" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('locations.destroy', $location) }}" class="inline" onsubmit="return confirm('Delete this location?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
