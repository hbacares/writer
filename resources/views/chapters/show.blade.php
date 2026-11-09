<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $chapter->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('chapters.sections.create', $chapter) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('Add Section') }}
                </a>
                <a href="{{ route('chapters.edit', $chapter) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Edit Chapter') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($chapter->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $chapter->description }}</p>
                    @endif
                    <div class="flex gap-6 text-sm text-gray-500 dark:text-gray-400">
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Chapter Words:</span>
                            <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($chapter->word_count) }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Sections:</span>
                            <span>{{ $chapter->sections->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Sections') }}
                    </h3>

                    @if ($chapter->sections->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400 text-center py-8">
                            {{ __('No sections yet. Add your first section to start writing!') }}
                        </p>
                    @else
                        <div class="space-y-4" id="sections-list" data-parent-id="{{ $chapter->id }}">
                            @foreach ($chapter->sections as $section)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition" data-id="{{ $section->id }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-start gap-3 flex-1">
                                            <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 pt-1">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                            <h4 class="text-base font-medium text-gray-900 dark:text-gray-100">
                                                <a href="{{ route('sections.show', $section) }}" class="hover:underline">
                                                    Section {{ $section->order + 1 }}: {{ $section->title }}
                                                </a>
                                            </h4>
                                            <div class="flex gap-3 text-xs text-gray-500 dark:text-gray-500 mt-2">
                                                <span>{{ $section->paragraphs->count() }} {{ Str::plural('paragraph', $section->paragraphs->count()) }}</span>
                                                <span>{{ number_format($section->word_count) }} words</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 ml-4">
                                            <a href="{{ route('sections.edit', $section) }}" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('sections.destroy', $section) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this section?');">
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

            <div class="mt-6">
                <a href="{{ route('stories.show', $chapter->story) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                    ← {{ __('Back to Story') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
