@foreach ($news as $post)
<div class="col-lg-6 news-item-card">
    <!-- Post Article -->
    <div class="article__entry">
        <div class="article__image">
            <a href="{{ route('news-details', $post->slug) }}">
                <img src="{{ asset($post->image) }}" alt="" class="img-fluid" onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/placeholder.webp') }}';">
            </a>
        </div>
        <div class="article__content">
            <div class="article__category">
                {{ $post->category->name }}
            </div>
            <ul class="list-inline">
                <li class="list-inline-item">
                    <span class="text-primary">
                        {{ __('frontend.by') }} {{ $post->auther->name }}
                    </span>
                </li>
                <li class="list-inline-item">
                    <span class="text-dark text-capitalize">
                        {{ formatDate($post->created_at) }}
                    </span>
                </li>

            </ul>
            <h5>
                <a href="{{ route('news-details', $post->slug) }}">
                    {!! truncate($post->title) !!}
                </a>
            </h5>
            <p>
                {!! truncate($post->content, 100) !!}
            </p>
            <a href="{{ route('news-details', $post->slug) }}" class="btn btn-outline-primary mb-4 text-capitalize"> {{ __('frontend.read more') }}</a>
        </div>
    </div>
</div>
@endforeach

{{-- Carry next page URL --}}
@if ($news->hasMorePages())
    <div id="next-page-url" data-url="{{ $news->nextPageUrl() }}" style="display:none;"></div>
@endif
