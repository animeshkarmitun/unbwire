<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New Article Published</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .news-header {
            margin-bottom: 20px;
        }
        .news-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .news-meta {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .news-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin: 15px 0;
        }
        .image-placeholder {
            background-color: #e9ecef;
            padding: 40px;
            text-align: center;
            border-radius: 5px;
            color: #6c757d;
            margin: 15px 0;
        }
        .news-content {
            margin: 20px 0;
            line-height: 1.8;
        }
        .news-excerpt {
            margin: 20px 0;
            line-height: 1.8;
            font-style: italic;
            color: #495057;
        }
        .video-container {
            margin: 20px 0;
            text-align: center;
        }
        .video-upgrade, .exclusive-upgrade {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
        }
        .access-denied {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button-secondary {
            background-color: #6c757d;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 20px;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
        .unsubscribe-link {
            color: #6c757d;
            font-size: 11px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ getSetting('site_name') ?? 'UNB News' }}</h2>
        <p>New Article Published</p>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        <p>A new article has been published that you might be interested in:</p>

        @if($contentOptions['canAccessNews'])
            @if($templateSettings['include_title'] ?? true)
                <div class="news-header">
                    <h1 class="news-title">{{ $news->title }}</h1>
                    <div class="news-meta">
                        @if($templateSettings['include_category'] ?? true)
                            <span>{{ $news->category->name ?? 'News' }}</span>
                        @endif
                        @if($templateSettings['include_author'] ?? true)
                            @if($templateSettings['include_category'] ?? true) | @endif
                            <span>{{ $news->auther->name ?? ($news->author->name ?? 'Admin') }}</span>
                        @endif
                        @if($templateSettings['include_date'] ?? true)
                            @if(($templateSettings['include_category'] ?? true) || ($templateSettings['include_author'] ?? true)) | @endif
                            <span>{{ $news->created_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            @endif

            @if(($templateSettings['include_image'] ?? true) && $news->image)
                @if($contentOptions['includeImages'])
                    @if(file_exists(public_path($news->image)))
                        <img src="{{ $message->embed(public_path($news->image)) }}" alt="{{ $news->title }}" class="news-image" />
                    @else
                        <!-- Fallback to URL if local file not found or accessible -->
                        <img src="{{ asset($news->image) }}" alt="{{ $news->title }}" class="news-image" />
                    @endif
                @else
                    <div class="image-placeholder">
                        <p>📷 Image available with subscription</p>
                    </div>
                @endif
            @endif

            @if($templateSettings['include_content'] ?? true)
                @if($contentOptions['includeFullContent'] && ($templateSettings['send_full_content'] ?? true))
                    <div class="news-content">
                        {!! $news->content !!}
                    </div>
                @elseif($templateSettings['include_excerpt'] ?? true)
                    <div class="news-excerpt">
                        {{ Str::limit(strip_tags($news->content), 300) }}...
                    </div>
                    <p style="text-align: center; margin: 20px 0;">
                        <em>Subscribe to read the full article</em>
                    </p>
                @endif
            @endif

            @if(($templateSettings['include_tags'] ?? false) && $news->tags && $news->tags->count() > 0)
                <div style="margin: 20px 0;">
                    <strong>Tags:</strong>
                    @foreach($news->tags as $tag)
                        <span style="display: inline-block; background: #e9ecef; padding: 5px 10px; margin: 5px; border-radius: 3px;">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif

            @if(($templateSettings['include_video'] ?? true) && $news->video_url)
                @if($contentOptions['includeVideos'])
                    <div class="video-container">
                        <p><strong>Video Content:</strong></p>
                        <a href="{{ $news->video_url }}" class="button">Watch Video</a>
                    </div>
                @else
                    <div class="video-upgrade">
                        <p><strong>🎥 Video Content Available</strong></p>
                        <p>This article includes video content. Upgrade to Pro subscription to access videos.</p>
                        <a href="{{ route('subscription.plans') }}" class="button button-secondary">Upgrade to Pro</a>
                    </div>
                @endif
            @endif

            @if($news->is_exclusive && !$contentOptions['includeExclusive'])
                <div class="exclusive-upgrade">
                    <p><strong>⭐ Exclusive Content</strong></p>
                    <p>This is exclusive content. Upgrade to Ultra subscription to access exclusive articles.</p>
                    <a href="{{ route('subscription.plans') }}" class="button button-secondary">Upgrade to Ultra</a>
                </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('news-details', $news->slug) }}" class="button">Read Full Article</a>
            </div>

        @else
            <div class="access-denied">
                <p><strong>Premium Content</strong></p>
                <p>This content requires a subscription. Subscribe now to access premium articles.</p>
                <a href="{{ route('subscription.plans') }}" class="button">View Subscription Plans</a>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>This is an automated email from {{ getSetting('site_name') ?? 'UNB News' }}.</p>
        <p>
            <a href="{{ $unsubscribeLink }}" class="unsubscribe-link">
                Unsubscribe from email notifications
            </a>
        </p>
        <p>&copy; {{ date('Y') }} {{ getSetting('site_name') ?? 'UNB News' }}. All rights reserved.</p>
    </div>
</body>
</html>
