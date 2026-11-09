<?php

namespace App\Observers;

use App\Models\Paragraph;

class ParagraphObserver
{
    public function saving(Paragraph $paragraph): void
    {
        $paragraph->word_count = $this->calculateWordCount($paragraph->content);
    }

    public function saved(Paragraph $paragraph): void
    {
        $paragraph->section->updateWordCount();
    }

    public function deleted(Paragraph $paragraph): void
    {
        $paragraph->section->updateWordCount();
    }

    private function calculateWordCount(string $content): int
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (empty($text)) {
            return 0;
        }

        return str_word_count($text);
    }
}
