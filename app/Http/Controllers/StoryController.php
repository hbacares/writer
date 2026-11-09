<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class StoryController extends Controller
{
    public function index()
    {
        $stories = auth()->user()->stories()->with('chapters')->get();

        return view('stories.index', compact('stories'));
    }

    public function create()
    {
        return view('stories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = auth()->user()->stories()->max('order') ?? -1;

        $story = auth()->user()->stories()->create([
            ...$validated,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('stories.show', $story)
            ->with('success', 'Story created successfully.');
    }

    public function show(Story $story)
    {
        $this->authorize('view', $story);

        $story->load([
            'chapters.sections.paragraphs.characters',
            'chapters.sections.paragraphs.locations',
            'chapters.sections.paragraphs.references',
        ]);

        $readMode = request()->query('mode') === 'read';

        return view('stories.show', compact('story', 'readMode'));
    }

    public function edit(Story $story)
    {
        $this->authorize('update', $story);

        return view('stories.edit', compact('story'));
    }

    public function update(Request $request, Story $story)
    {
        $this->authorize('update', $story);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $story->update($validated);

        return redirect()->route('stories.show', $story)
            ->with('success', 'Story updated successfully.');
    }

    public function destroy(Story $story)
    {
        $this->authorize('delete', $story);

        $story->delete();

        return redirect()->route('stories.index')
            ->with('success', 'Story deleted successfully.');
    }

    public function reorderChapters(Request $request, Story $story)
    {
        $this->authorize('update', $story);

        $validated = $request->validate([
            'chapters' => 'required|array',
            'chapters.*' => 'required|integer|exists:chapters,id',
        ]);

        foreach ($validated['chapters'] as $order => $chapterId) {
            $story->chapters()->where('id', $chapterId)->update(['order' => $order]);
        }

        return back()->with('success', 'Chapters reordered successfully.');
    }

    public function exportPdf(Story $story)
    {
        $this->authorize('view', $story);

        $story->load(['chapters.sections.paragraphs']);

        $pdf = Pdf::loadView('stories.export-pdf', compact('story'));

        return $pdf->download(str($story->title)->slug().'.pdf');
    }

    public function exportDocx(Story $story)
    {
        $this->authorize('view', $story);

        $story->load(['chapters.sections.paragraphs']);

        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $section->addTitle($story->title, 1);

        if ($story->description) {
            $section->addText($story->description);
            $section->addTextBreak(1);
        }

        foreach ($story->chapters as $chapter) {
            $section->addTitle('Chapter '.($chapter->order + 1).': '.$chapter->title, 2);

            if ($chapter->description) {
                $section->addText($chapter->description);
                $section->addTextBreak(1);
            }

            foreach ($chapter->sections as $sectionModel) {
                $section->addTitle($sectionModel->title, 3);

                foreach ($sectionModel->paragraphs as $paragraph) {
                    Html::addHtml($section, $paragraph->content);
                    $section->addTextBreak(1);
                }
            }
        }

        $fileName = str($story->title)->slug().'.docx';
        $tempFile = storage_path('app/temp/'.$fileName);

        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend();
    }

    public function grammarAi(Story $story)
    {
        $this->authorize('view', $story);

        return view('stories.grammar-ai', compact('story'));
    }

    public function updateGrammarPrompt(Request $request, Story $story)
    {
        $this->authorize('update', $story);

        $validated = $request->validate([
            'grammar_prompt' => 'nullable|string',
        ]);

        $story->update($validated);

        return back()->with('success', 'Grammar AI prompt saved successfully.');
    }

    public function addSection(Story $story)
    {
        $this->authorize('update', $story);

        $lastChapter = $story->chapters()->orderBy('order', 'desc')->first();

        if (! $lastChapter) {
            return redirect()->route('stories.show', $story)
                ->with('error', 'Please create a chapter first before adding a section.');
        }

        return redirect()->route('chapters.sections.create', $lastChapter);
    }

    public function addParagraph(Story $story)
    {
        $this->authorize('update', $story);

        $lastChapter = $story->chapters()->orderBy('order', 'desc')->first();

        if (! $lastChapter) {
            return redirect()->route('stories.show', $story)
                ->with('error', 'Please create a chapter first before adding a paragraph.');
        }

        $lastSection = $lastChapter->sections()->orderBy('order', 'desc')->first();

        if (! $lastSection) {
            return redirect()->route('stories.show', $story)
                ->with('error', 'Please create a section first before adding a paragraph.');
        }

        return redirect()->route('sections.paragraphs.create', $lastSection);
    }

    public function magicTimeline(Story $story)
    {
        $this->authorize('view', $story);

        // Only allow for story ID 1
        if ($story->id !== 1) {
            abort(404);
        }

        return view('stories.magic-timeline', compact('story'));
    }
}
