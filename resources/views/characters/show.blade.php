<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $character->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('characters.edit', $character) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">Edit</a>
                <a href="{{ route('stories.characters.index', $character->story) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">Back</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-blue-100 dark:bg-blue-900 border border-blue-400 text-blue-700 dark:text-blue-300 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                @if ($character->image)
                    <img src="{{ asset('storage/' . $character->image) }}" alt="{{ $character->name }}" class="w-full h-96 object-cover">
                @endif
                <div class="p-6">
                    @if ($character->description)
                        <div class="prose dark:prose-invert max-w-none whitespace-pre-wrap mb-6">{{ $character->description }}</div>
                    @endif

                    @if ($character->paragraphs->count() > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Appears in {{ $character->paragraphs->count() }} {{ Str::plural('paragraph', $character->paragraphs->count()) }}</h3>
                            <div class="space-y-3">
                                @foreach ($character->paragraphs as $paragraph)
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            <a href="{{ route('stories.show', $character->story) }}#paragraph-{{ $paragraph->id }}" class="hover:underline">
                                                {{ $paragraph->section->chapter->title }} → {{ $paragraph->section->title }}
                                            </a>
                                        </div>
                                        <div class="prose dark:prose-invert prose-sm max-w-none">
                                            {!! Str::limit(strip_tags($paragraph->content), 200) !!}
                                        </div>
                                        <a href="{{ route('paragraphs.edit', $paragraph) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">
                                            Edit paragraph
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
