<?php

use App\Http\Controllers\AiItemController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ParagraphController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\ScratchpadController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('stories.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('stories', StoryController::class);
    Route::resource('stories.chapters', ChapterController::class)->shallow();
    Route::resource('chapters.sections', SectionController::class)->shallow();
    Route::resource('sections.paragraphs', ParagraphController::class)->shallow();
    Route::resource('stories.scratchpads', ScratchpadController::class)->except(['show'])->shallow();
    Route::resource('stories.locations', LocationController::class)->shallow();
    Route::resource('stories.characters', CharacterController::class)->shallow();
    Route::resource('stories.references', ReferenceController::class)->shallow();

    Route::post('stories/{story}/reorder-chapters', [StoryController::class, 'reorderChapters'])->name('stories.reorder-chapters');
    Route::post('chapters/{chapter}/reorder-sections', [ChapterController::class, 'reorderSections'])->name('chapters.reorder-sections');
    Route::post('sections/{section}/reorder-paragraphs', [SectionController::class, 'reorderParagraphs'])->name('sections.reorder-paragraphs');
    Route::post('paragraphs/{paragraph}/merge-with-previous', [ParagraphController::class, 'mergeWithPrevious'])->name('paragraphs.merge-with-previous');
    Route::post('paragraphs/{paragraph}/ai-suggestions', [ParagraphController::class, 'aiSuggestions'])->name('paragraphs.ai-suggestions');
    Route::get('stories/{story}/add-section', [StoryController::class, 'addSection'])->name('stories.add-section');
    Route::get('stories/{story}/add-paragraph', [StoryController::class, 'addParagraph'])->name('stories.add-paragraph');

    Route::get('stories/{story}/export/pdf', [StoryController::class, 'exportPdf'])->name('stories.export-pdf');
    Route::get('stories/{story}/export/docx', [StoryController::class, 'exportDocx'])->name('stories.export-docx');
    Route::get('stories/{story}/grammar-ai', [StoryController::class, 'grammarAi'])->name('stories.grammar-ai');
    Route::post('stories/{story}/grammar-ai', [StoryController::class, 'updateGrammarPrompt'])->name('stories.update-grammar-prompt');
    Route::get('stories/{story}/magic-timeline', [StoryController::class, 'magicTimeline'])->name('stories.magic-timeline');

    Route::post('scratchpads/{scratchpad}/move', [ScratchpadController::class, 'moveToSection'])->name('scratchpads.move');

    // AI item confirmation/rejection
    Route::post('characters/{character}/confirm', [AiItemController::class, 'confirmCharacter'])->name('characters.confirm');
    Route::post('characters/{character}/reject', [AiItemController::class, 'rejectCharacter'])->name('characters.reject');
    Route::post('locations/{location}/confirm', [AiItemController::class, 'confirmLocation'])->name('locations.confirm');
    Route::post('locations/{location}/reject', [AiItemController::class, 'rejectLocation'])->name('locations.reject');
    Route::post('references/{reference}/confirm', [AiItemController::class, 'confirmReference'])->name('references.confirm');
    Route::post('references/{reference}/reject', [AiItemController::class, 'rejectReference'])->name('references.reject');

    // Entity conversions
    Route::post('characters/{character}/convert-to-location', [CharacterController::class, 'convertToLocation'])->name('characters.convert-to-location');
    Route::post('characters/{character}/convert-to-reference', [CharacterController::class, 'convertToReference'])->name('characters.convert-to-reference');
    Route::post('locations/{location}/convert-to-character', [LocationController::class, 'convertToCharacter'])->name('locations.convert-to-character');
    Route::post('locations/{location}/convert-to-reference', [LocationController::class, 'convertToReference'])->name('locations.convert-to-reference');
    Route::post('references/{reference}/convert-to-character', [ReferenceController::class, 'convertToCharacter'])->name('references.convert-to-character');
    Route::post('references/{reference}/convert-to-location', [ReferenceController::class, 'convertToLocation'])->name('references.convert-to-location');
});

require __DIR__.'/auth.php';
