<?php

namespace App\Providers;

use App\Models\Chapter;
use App\Models\Paragraph;
use App\Models\Section;
use App\Models\Story;
use App\Observers\ChapterObserver;
use App\Observers\ParagraphObserver;
use App\Observers\SectionObserver;
use App\Observers\StoryObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paragraph::observe(ParagraphObserver::class);
        Section::observe(SectionObserver::class);
        Chapter::observe(ChapterObserver::class);
        Story::observe(StoryObserver::class);
    }
}
