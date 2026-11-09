<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $reference->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('references.edit', $reference) }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 transition">Edit</a>
                <a href="{{ route('stories.references.index', $reference->story) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">Back</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-amber-100 dark:bg-amber-900 border border-amber-400 text-amber-700 dark:text-amber-300 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                @if ($reference->image)
                    <img src="{{ asset('storage/' . $reference->image) }}" alt="{{ $reference->title }}" class="w-full h-96 object-cover">
                @endif
                <div class="p-6">
                    @if ($reference->url)
                        <div class="mb-4">
                            <a href="{{ $reference->url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $reference->url }}</a>
                        </div>
                    @endif
                    @if ($reference->content)
                        <div class="prose dark:prose-invert max-w-none whitespace-pre-wrap mb-6">{{ $reference->content }}</div>
                    @endif

                    @if ($reference->paragraphs->count() > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Referenced in {{ $reference->paragraphs->count() }} {{ Str::plural('paragraph', $reference->paragraphs->count()) }}</h3>
                            <div class="space-y-3">
                                @foreach ($reference->paragraphs as $paragraph)
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            <a href="{{ route('stories.show', $reference->story) }}#paragraph-{{ $paragraph->id }}" class="hover:underline">
                                                {{ $paragraph->section->chapter->title }} → {{ $paragraph->section->title }}
                                            </a>
                                        </div>
                                        <div class="prose dark:prose-invert prose-sm max-w-none">
                                            {!! Str::limit(strip_tags($paragraph->content), 200) !!}
                                        </div>
                                        <a href="{{ route('paragraphs.edit', $paragraph) }}" class="text-xs text-amber-600 dark:text-amber-400 hover:underline mt-2 inline-block">
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
