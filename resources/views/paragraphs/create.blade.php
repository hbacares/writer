<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Paragraph to') }}: {{ $section->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('sections.paragraphs.store', $section) }}">
                        @csrf

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Content') }}
                            </label>
                            <input id="content" type="hidden" name="content" value="{{ old('content') }}">
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
                                        <option value="{{ $character->id }}" {{ (is_array(old('characters')) && in_array($character->id, old('characters'))) ? 'selected' : '' }}>
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
                                        <option value="{{ $location->id }}" {{ (is_array(old('locations')) && in_array($location->id, old('locations'))) ? 'selected' : '' }}>
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
                                        <option value="{{ $reference->id }}" {{ (is_array(old('references')) && in_array($reference->id, old('references'))) ? 'selected' : '' }}>
                                            {{ $reference->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Create Paragraph') }}
                            </button>
                            <a href="{{ route('sections.show', $section) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
