<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Stories') }}
            </h2>
            <a href="{{ route('stories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('New Story') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if ($stories->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p class="text-center">{{ __('No stories yet. Create your first story!') }}</p>
                    </div>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($stories as $story)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('stories.show', $story) }}" class="hover:underline">
                                            {{ $story->title }}
                                        </a>
                                    </h3>
                                </div>

                                @if ($story->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                        {{ Str::limit($story->description, 100) }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <div class="flex gap-3">
                                        <span>{{ $story->chapters->count() }} {{ Str::plural('chapter', $story->chapters->count()) }}</span>
                                        <span>{{ number_format($story->word_count) }} words</span>
                                    </div>
                                    <span>{{ $story->updated_at->diffForHumans() }}</span>
                                </div>

                                <div class="mt-4 flex gap-2">
                                    <a href="{{ route('stories.show', $story) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                        View
                                    </a>
                                    <a href="{{ route('stories.edit', $story) }}" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('stories.destroy', $story) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this story?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                            Delete
                                        </button>
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
