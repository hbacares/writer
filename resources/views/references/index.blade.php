<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">References: {{ $story->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('stories.references.create', $story) }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 transition">New Reference</a>
                <a href="{{ route('stories.show', $story) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">Back</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-amber-100 dark:bg-amber-900 border border-amber-400 text-amber-700 dark:text-amber-300 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if ($references->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-center text-gray-600 dark:text-gray-400">No references yet. Create your first reference!</div>
            @else
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($references as $reference)
                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                            @if ($reference->image)
                                <img src="{{ asset('storage/' . $reference->image) }}" alt="{{ $reference->title }}" class="w-full h-48 object-cover">
                            @endif
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $reference->title }}</h3>
                                @if ($reference->url)
                                    <a href="{{ $reference->url }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mb-2 block">{{ $reference->url }}</a>
                                @endif
                                @if ($reference->content)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($reference->content, 150) }}</p>
                                @endif
                                <div class="flex gap-2">
                                    <a href="{{ route('references.show', $reference) }}" class="text-xs text-amber-600 dark:text-amber-400 hover:underline">View</a>
                                    <a href="{{ route('references.edit', $reference) }}" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('references.destroy', $reference) }}" class="inline" onsubmit="return confirm('Delete this reference?');">
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
