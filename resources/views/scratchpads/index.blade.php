<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Scratchpads') }}: {{ $story->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('stories.scratchpads.create', $story) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('New Scratchpad') }}
                </a>
                <a href="{{ route('stories.show', $story) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Back to Story') }}
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

            @if ($scratchpads->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p class="text-center">{{ __('No scratchpads yet. Create your first scratchpad to start brainstorming!') }}</p>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($scratchpads as $scratchpad)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-start gap-4 mb-4">
                                    <div class="flex-1">
                                        @if ($scratchpad->title)
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                {{ $scratchpad->title }}
                                            </h3>
                                        @endif
                                    </div>
                                    <div class="flex gap-2 flex-shrink-0">
                                        <a href="{{ route('scratchpads.edit', $scratchpad) }}" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('scratchpads.destroy', $scratchpad) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this scratchpad?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="prose dark:prose-invert max-w-none mb-4">
                                    {!! $scratchpad->content !!}
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <form method="POST" action="{{ route('scratchpads.move', $scratchpad) }}" class="flex items-end gap-2" x-data="{ section: '' }">
                                        @csrf
                                        <div class="flex-1">
                                            <label for="section_{{ $scratchpad->id }}" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ __('Move to Section') }}
                                            </label>
                                            <select name="section_id" id="section_{{ $scratchpad->id }}" x-model="section" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 text-sm" required>
                                                <option value="">Select a section...</option>
                                                @foreach ($story->chapters as $chapter)
                                                    <optgroup label="Chapter {{ $chapter->order + 1 }}: {{ $chapter->title }}">
                                                        @foreach ($chapter->sections as $section)
                                                            <option value="{{ $section->id }}">{{ $section->title }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" x-bind:disabled="!section" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                            {{ __('Move') }}
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
