<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Paragraph') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="paragraphEditor()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('paragraphs.update', $paragraph) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Content') }}
                            </label>
                            <input id="content" type="hidden" name="content" value="{{ old('content', $paragraph->content) }}">
                            <trix-editor input="content" class="trix-content"></trix-editor>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            {{-- Characters --}}
                            <div>
                                <label for="characters" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Characters
                                </label>
                                <select name="characters[]" id="characters" multiple class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-32">
                                    @foreach ($characters as $character)
                                        <option value="{{ $character->id }}" {{ $paragraph->characters->contains($character->id) ? 'selected' : '' }}>
                                            {{ $character->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple</p>
                            </div>

                            {{-- Locations --}}
                            <div>
                                <label for="locations" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Locations
                                </label>
                                <select name="locations[]" id="locations" multiple class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 h-32">
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" {{ $paragraph->locations->contains($location->id) ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple</p>
                            </div>

                            {{-- References --}}
                            <div>
                                <label for="references" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    References
                                </label>
                                <select name="references[]" id="references" multiple class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 h-32">
                                    @foreach ($references as $reference)
                                        <option value="{{ $reference->id }}" {{ $paragraph->references->contains($reference->id) ? 'selected' : '' }}>
                                            {{ $reference->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
                                {{ __('Update Paragraph') }}
                            </button>
                            <button type="button" @click="getAISuggestions" :disabled="loading" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition disabled:opacity-50">
                                <svg class="w-4 h-4 mr-2" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                <span x-text="loading ? 'Analyzing...' : 'AI Suggestions'"></span>
                            </button>
                            <a href="{{ route('sections.show', $paragraph->section) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>

                    {{-- AI Suggestions Panel --}}
                    <div x-show="suggestions" x-cloak class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="bg-emerald-50 dark:bg-emerald-900 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100 mb-4">
                                AI Grammar Suggestions
                            </h3>

                            {{-- Summary --}}
                            <div class="mb-6">
                                <p class="text-sm text-emerald-800 dark:text-emerald-200" x-text="suggestions?.feedback?.summary"></p>
                            </div>

                            {{-- Changes --}}
                            <div x-show="suggestions?.feedback?.changes?.length > 0" class="mb-6">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">Suggested Changes:</h4>
                                <div class="space-y-2">
                                    <template x-for="change in suggestions?.feedback?.changes" :key="change.id">
                                        <div class="bg-white dark:bg-emerald-800 rounded p-3">
                                            <div class="flex items-start gap-2 mb-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                      :class="{
                                                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': change.type === 'error',
                                                          'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': change.type === 'clarity',
                                                          'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': change.type === 'enhancement',
                                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200': change.type === 'optional'
                                                      }"
                                                      x-text="change.type"></span>
                                                <span class="text-xs text-gray-600 dark:text-gray-300" x-text="change.category"></span>
                                            </div>
                                            <div class="text-sm space-y-1">
                                                <div><span class="font-medium">Original:</span> <span class="line-through text-gray-600 dark:text-gray-400" x-text="change.original"></span></div>
                                                <div><span class="font-medium">Suggestion:</span> <span class="text-emerald-700 dark:text-emerald-300" x-text="change.suggestion"></span></div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400" x-text="change.reason"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Questions --}}
                            <div x-show="suggestions?.feedback?.questions?.length > 0" class="mb-6">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">Questions for you:</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-emerald-800 dark:text-emerald-200">
                                    <template x-for="question in suggestions?.feedback?.questions" :key="question">
                                        <li x-text="question"></li>
                                    </template>
                                </ul>
                            </div>

                            {{-- Rewrite Preview --}}
                            <div x-show="suggestions?.rewrite" class="mb-6">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">Rewritten Text:</h4>
                                <div class="bg-white dark:bg-emerald-800 rounded p-4 text-sm text-gray-900 dark:text-gray-100 leading-relaxed"
                                     x-html="formatRewrite(suggestions?.rewrite)"></div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-3">
                                <button @click="acceptSuggestions" type="button" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Accept Changes
                                </button>
                                <button @click="suggestions = null" type="button" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Error Message --}}
                    <div x-show="error" x-cloak class="mt-4 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">
                        <p x-text="error"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function paragraphEditor() {
            return {
                loading: false,
                suggestions: null,
                error: null,

                async getAISuggestions() {
                    this.loading = true;
                    this.error = null;
                    this.suggestions = null;

                    try {
                        const response = await fetch('{{ route('paragraphs.ai-suggestions', $paragraph) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.error = data.error || 'Failed to get suggestions';
                            return;
                        }

                        this.suggestions = data;
                    } catch (err) {
                        this.error = 'Network error: ' + err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                formatRewrite(text) {
                    if (!text) return '';

                    // Trim the entire text first
                    text = text.trim();

                    // Escape HTML entities for security
                    const escapeHtml = (str) => {
                        const div = document.createElement('div');
                        div.textContent = str;
                        return div.innerHTML;
                    };

                    // Split into paragraphs by double newlines (or more)
                    const paragraphs = text.split(/\n\n+/);

                    // Process each paragraph - use <div> to match Trix format
                    return paragraphs
                        .map(para => para.trim()) // Trim each paragraph first
                        .filter(para => para.length > 0) // Remove empty paragraphs
                        .map(para => {
                            // Escape HTML and convert single newlines to <br>
                            const escaped = escapeHtml(para);
                            const withBreaks = escaped.replace(/\n/g, '<br>');
                            return '<div>' + withBreaks + '</div>';
                        })
                        .join('');
                },

                acceptSuggestions() {
                    if (!this.suggestions?.rewrite) return;

                    // Get Trix editor
                    const trixEditor = document.querySelector('trix-editor');
                    if (trixEditor) {
                        // Convert newlines to HTML for Trix editor
                        let htmlContent = this.formatRewrite(this.suggestions.rewrite);

                        // Clean up any empty divs that might have been created
                        htmlContent = htmlContent.replace(/<div>\s*<\/div>/g, '');
                        htmlContent = htmlContent.replace(/<div><br><\/div>/g, '');

                        // Load into Trix
                        trixEditor.editor.loadHTML(htmlContent);
                    }

                    // Clear suggestions
                    this.suggestions = null;

                    // Show success message
                    alert('Changes applied! Don\'t forget to save the paragraph.');
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
