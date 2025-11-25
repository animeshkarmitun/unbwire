<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #d32f2f;
            border-bottom: 3px solid #d32f2f;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .meta-info {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .meta-info p {
            margin: 5px 0;
        }
        .content {
            text-align: justify;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        img {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>{{ $news->title }}</h1>
    
    <div class="meta-info">
        <p><strong>Author:</strong> {{ $news->auther->name ?? 'N/A' }}</p>
        <p><strong>Category:</strong> {{ $news->category->name ?? 'N/A' }}</p>
        <p><strong>Published Date:</strong> {{ $news->created_at->format('F d, Y') }}</p>
        <p><strong>Views:</strong> {{ number_format($news->views) }}</p>
        @if($news->tags->isNotEmpty())
        <p><strong>Tags:</strong> {{ $news->tags->pluck('name')->implode(', ') }}</p>
        @endif
    </div>

    @if($news->image)
    <div style="text-align: center;">
        <img src="{{ public_path($news->image) }}" alt="{{ $news->title }}">
    </div>
    @endif

    <div class="content">
        {!! strip_tags($news->content, '<p><br><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6>') !!}
    </div>

    <div class="footer">
        <p>Exported from {{ config('app.name') }} on {{ now()->format('F d, Y \a\t H:i') }}</p>
        <p>URL: {{ url()->current() }}</p>
    </div>
</body>
</html>


