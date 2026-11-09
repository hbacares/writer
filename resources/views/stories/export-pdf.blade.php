<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $story->title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        h2 {
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        h3 {
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 15px;
        }
        .description {
            font-style: italic;
            color: #666;
            margin-bottom: 20px;
        }
        .word-count {
            color: #999;
            font-size: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>{{ $story->title }}</h1>

    @if ($story->description)
        <p class="description">{{ $story->description }}</p>
    @endif

    <p class="word-count">Total Words: {{ number_format($story->word_count) }}</p>

    @foreach ($story->chapters as $chapter)
        <h2>Chapter {{ $chapter->order + 1 }}: {{ $chapter->title }}</h2>

        @if ($chapter->description)
            <p class="description">{{ $chapter->description }}</p>
        @endif

        @foreach ($chapter->sections as $section)
            <h3>{{ $section->title }}</h3>

            @foreach ($section->paragraphs as $paragraph)
                <div>{!! $paragraph->content !!}</div>
            @endforeach
        @endforeach
    @endforeach
</body>
</html>
