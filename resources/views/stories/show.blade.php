<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $story->title }}
                </h2>
                <a href="{{ route('stories.edit', $story) }}" class="inline-flex items-center p-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition" title="Edit Story">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
            </div>
            <div class="flex gap-2 flex-wrap items-center">
                {{-- Actions Dropdown --}}
                <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Actions
                        <svg class="-mr-1 ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1">
                            <a href="{{ route('stories.chapters.create', $story) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Chapter
                                </div>
                            </a>
                            <a href="{{ route('stories.add-section', $story) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Section
                                </div>
                            </a>
                            <a href="{{ route('stories.add-paragraph', $story) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Paragraph
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Resources Dropdown --}}
                <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center px-3 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
                        Resources
                        <svg class="-mr-1 ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1">
                            <a href="{{ route('stories.scratchpads.index', $story) }}" class="block px-4 py-2 text-sm text-purple-600 dark:text-purple-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Scratchpads
                            </a>
                            <a href="{{ route('stories.locations.index', $story) }}" class="block px-4 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Locations
                            </a>
                            <a href="{{ route('stories.characters.index', $story) }}" class="block px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Characters
                            </a>
                            <a href="{{ route('stories.references.index', $story) }}" class="block px-4 py-2 text-sm text-amber-600 dark:text-amber-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                References
                            </a>
                            @if ($story->id === 1)
                                <a href="{{ route('stories.magic-timeline', $story) }}" class="block px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Magic Aspects Timeline
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Grammar AI Button --}}
                <a href="{{ route('stories.grammar-ai', $story) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Grammar AI
                </a>

                {{-- Export Dropdown --}}
                <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Export
                        <svg class="-mr-1 ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1">
                            <a href="{{ route('stories.export-pdf', $story) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Export as PDF
                            </a>
                            <a href="{{ route('stories.export-docx', $story) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Export as DOCX
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if ($story->description)
                <div class="mb-8 text-gray-600 dark:text-gray-400 italic">
                    {{ $story->description }}
                </div>
            @endif

            <div class="space-y-8">
                @forelse ($story->chapters as $chapterIndex => $chapter)
                    {{-- Chapter Container --}}
                    <div class="group relative" id="chapter-{{ $chapter->id }}">
                        {{-- Chapter Marker --}}
                        <div class="flex items-start gap-3 mb-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                Chapter {{ $chapterIndex + 1 }}
                            </span>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex-1">
                                {{ $chapter->title }}
                            </h2>
                            {{-- Edit Button --}}
                            <a href="{{ route('chapters.edit', $chapter) }}" class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity inline-flex items-center px-2 py-1 text-xs text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 border border-gray-300 dark:border-gray-600 rounded hover:border-indigo-400 dark:hover:border-indigo-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                        </div>

                        @if ($chapter->description)
                            <p class="mb-6 text-gray-600 dark:text-gray-400 italic pl-4 border-l-2 border-indigo-200 dark:border-indigo-800">
                                {{ $chapter->description }}
                            </p>
                        @endif

                        {{-- Sections --}}
                        <div class="space-y-6 pl-4">
                            @forelse ($chapter->sections as $section)
                                <div class="group/section relative" id="section-{{ $section->id }}">
                                    {{-- Section Marker --}}
                                    <div class="flex items-start gap-3 mb-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                            Section
                                        </span>
                                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex-1">
                                            {{ $section->title }}
                                        </h3>
                                        {{-- Edit Button --}}
                                        <a href="{{ route('sections.edit', $section) }}" class="opacity-100 md:opacity-0 md:group-hover/section:opacity-100 transition-opacity inline-flex items-center px-2 py-1 text-xs text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 border border-gray-300 dark:border-gray-600 rounded hover:border-purple-400 dark:hover:border-purple-500">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                    </div>

                                    {{-- Paragraphs --}}
                                    <div class="space-y-4 pl-4">
                                        @forelse ($section->paragraphs as $paragraph)
                                            <div class="group/paragraph relative" id="paragraph-{{ $paragraph->id }}">
                                                <div class="flex items-start gap-3">
                                                    {{-- Left Side: Markers and Tags --}}
                                                    <div class="flex flex-col gap-1 self-start" x-data="{
                                                        showParagraphMenu: false,
                                                        showCharacters: false,
                                                        showLocations: false,
                                                        showReferences: false
                                                    }">
                                                        {{-- Paragraph Marker --}}
                                                        <div class="relative">
                                                            <button @click="showParagraphMenu = !showParagraphMenu" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition cursor-pointer">
                                                                ¶
                                                            </button>
                                                            <div x-show="showParagraphMenu"
                                                                 @click.away="showParagraphMenu = false"
                                                                 x-cloak
                                                                 class="absolute left-full ml-2 top-0 z-50 bg-white dark:bg-gray-800 border-2 border-gray-400 dark:border-gray-500 rounded-lg shadow-xl p-1 whitespace-nowrap min-w-[200px]">
                                                                {{-- Menu Header --}}
                                                                <div class="px-3 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 mb-1">
                                                                    Paragraph #{{ $paragraph->id }} (Order: {{ $paragraph->order }})
                                                                </div>

                                                                @php
                                                                    // Check if there's a paragraph with lower order (not just loop position)
                                                                    $hasPreviousParagraph = $section->paragraphs->where('order', '<', $paragraph->order)->isNotEmpty();
                                                                    $previousParagraph = $hasPreviousParagraph ? $section->paragraphs->where('order', '<', $paragraph->order)->sortByDesc('order')->first() : null;
                                                                @endphp
                                                                @if ($hasPreviousParagraph)
                                                                    <form method="POST"
                                                                          action="{{ route('paragraphs.merge-with-previous', $paragraph) }}"
                                                                          class="block"
                                                                          onsubmit="return confirm('Merge paragraph #{{ $paragraph->id }} into paragraph #{{ $previousParagraph->id }}?\n\nThis will combine the content and delete paragraph #{{ $paragraph->id }}. This action cannot be undone.')">
                                                                        @csrf
                                                                        <button type="submit" class="w-full text-left px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded flex items-center gap-2">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                                                            </svg>
                                                                            <span>Merge into #{{ $previousParagraph->id }}</span>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <div class="px-3 py-1.5 text-xs text-gray-400 dark:text-gray-500">
                                                                        First paragraph
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- Characters Toggle --}}
                                                        @if ($paragraph->characters->count() > 0)
                                                            <div class="relative">
                                                                <button @click="showCharacters = !showCharacters" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition cursor-pointer">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                    </svg>
                                                                    <span class="ml-0.5">{{ $paragraph->characters->count() }}</span>
                                                                </button>
                                                                <div x-show="showCharacters" @click.away="showCharacters = false" class="absolute left-full ml-2 top-0 z-10 bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 rounded-lg shadow-lg p-2 whitespace-nowrap">
                                                                    @foreach ($paragraph->characters as $character)
                                                                        <a href="{{ route('characters.show', $character) }}" class="block px-2 py-1 text-sm text-blue-700 dark:text-blue-200 hover:bg-blue-50 dark:hover:bg-blue-900 rounded">
                                                                            {{ $character->name }}
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- Locations Toggle --}}
                                                        @if ($paragraph->locations->count() > 0)
                                                            <div class="relative">
                                                                <button @click="showLocations = !showLocations" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800 transition cursor-pointer">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                    <span class="ml-0.5">{{ $paragraph->locations->count() }}</span>
                                                                </button>
                                                                <div x-show="showLocations" @click.away="showLocations = false" class="absolute left-full ml-2 top-0 z-10 bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 rounded-lg shadow-lg p-2 whitespace-nowrap">
                                                                    @foreach ($paragraph->locations as $location)
                                                                        <a href="{{ route('locations.show', $location) }}" class="block px-2 py-1 text-sm text-green-700 dark:text-green-200 hover:bg-green-50 dark:hover:bg-green-900 rounded">
                                                                            {{ $location->name }}
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- References Toggle --}}
                                                        @if ($paragraph->references->count() > 0)
                                                            <div class="relative">
                                                                <button @click="showReferences = !showReferences" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200 hover:bg-amber-200 dark:hover:bg-amber-800 transition cursor-pointer">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    <span class="ml-0.5">{{ $paragraph->references->count() }}</span>
                                                                </button>
                                                                <div x-show="showReferences" @click.away="showReferences = false" class="absolute left-full ml-2 top-0 z-10 bg-white dark:bg-gray-800 border border-amber-200 dark:border-amber-700 rounded-lg shadow-lg p-2 whitespace-nowrap">
                                                                    @foreach ($paragraph->references as $reference)
                                                                        <a href="{{ route('references.show', $reference) }}" class="block px-2 py-1 text-sm text-amber-700 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-amber-900 rounded">
                                                                            {{ $reference->title }}
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Paragraph Content --}}
                                                    <div class="flex-1 prose dark:prose-invert max-w-none prose-sm">
                                                        {!! $paragraph->content !!}
                                                    </div>

                                                    {{-- Edit Button --}}
                                                    <a href="{{ route('paragraphs.edit', $paragraph) }}" class="opacity-100 md:opacity-0 md:group-hover/paragraph:opacity-100 transition-opacity inline-flex items-center px-2 py-1 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded hover:border-gray-400 dark:hover:border-gray-500 mt-1 flex-shrink-0 self-start">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-sm text-gray-500 dark:text-gray-400 italic pl-4">
                                                No paragraphs yet.
                                                <a href="{{ route('sections.paragraphs.create', $section) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    Add the first paragraph
                                                </a>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500 dark:text-gray-400 italic pl-4">
                                    No sections yet.
                                    <a href="{{ route('chapters.sections.create', $chapter) }}" class="text-purple-600 dark:text-purple-400 hover:underline">
                                        Add the first section
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No chapters yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first chapter.</p>
                        <div class="mt-6">
                            <a href="{{ route('stories.chapters.create', $story) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create First Chapter
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Bottom Action Buttons --}}
            <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-center gap-3">
                    <a href="{{ route('stories.chapters.create', $story) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 border border-indigo-300 dark:border-indigo-500/50 rounded-md text-xs font-medium hover:bg-indigo-200 dark:hover:bg-indigo-500/30 hover:border-indigo-400 dark:hover:border-indigo-400 transition">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Chapter
                    </a>
                    <a href="{{ route('stories.add-section', $story) }}" class="inline-flex items-center px-3 py-1.5 bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400 border border-purple-300 dark:border-purple-500/50 rounded-md text-xs font-medium hover:bg-purple-200 dark:hover:bg-purple-500/30 hover:border-purple-400 dark:hover:border-purple-400 transition">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Section
                    </a>
                    <a href="{{ route('stories.add-paragraph', $story) }}" class="inline-flex items-center px-3 py-1.5 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 border border-green-300 dark:border-green-500/50 rounded-md text-xs font-medium hover:bg-green-200 dark:hover:bg-green-500/30 hover:border-green-400 dark:hover:border-green-400 transition">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Paragraph
                    </a>
                </div>
            </div>

            @if ($story->chapters->count() > 0)
                <div class="mt-6 text-sm text-gray-500 dark:text-gray-400 text-center">
                    {{ number_format($story->word_count) }} words total
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
