<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Grammar AI Prompt - {{ $story->title }}
            </h2>
            <a href="{{ route('stories.show', $story) }}" class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Back to Story
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Grammar & Style Instructions
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Set custom grammar and style guidelines for AI-powered editing
                                </p>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">
                                💡 How to use this prompt
                            </h4>
                            <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                                <li>• Describe your preferred writing style and grammar rules</li>
                                <li>• Specify tone: formal, casual, poetic, technical, etc.</li>
                                <li>• Set punctuation preferences (Oxford comma, em-dashes, etc.)</li>
                                <li>• Define vocabulary level and complexity</li>
                                <li>• This prompt will be used for AI-powered grammar checking and editing</li>
                            </ul>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('stories.update-grammar-prompt', $story) }}">
                        @csrf

                        <div class="mb-6">
                            <label for="grammar_prompt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Grammar & Style Prompt
                            </label>
                            <textarea
                                name="grammar_prompt"
                                id="grammar_prompt"
                                rows="12"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 font-mono text-sm"
                                placeholder="Example:&#10;&#10;Please edit this text following these guidelines:&#10;- Use British English spelling&#10;- Maintain a formal, literary tone&#10;- Prefer active voice over passive&#10;- Use em-dashes for parenthetical statements&#10;- Keep sentences concise (max 25 words)&#10;- Avoid adverbs where possible&#10;- Use the Oxford comma&#10;&#10;Focus on clarity and elegance while preserving my unique voice and style."
                            >{{ old('grammar_prompt', $story->grammar_prompt) }}</textarea>
                            @error('grammar_prompt')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                This prompt will be used when AI analyzes and suggests improvements to your text
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Prompt
                            </button>
                            <a href="{{ route('stories.show', $story) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                Cancel
                            </a>
                        </div>
                    </form>

                    @if ($story->grammar_prompt)
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                Current Prompt Preview
                            </h4>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono">{{ $story->grammar_prompt }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
