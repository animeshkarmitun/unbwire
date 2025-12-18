<section>
    <!-- Popular news  header-->
    <div class="popular__news-header">
        <div class="container">
            <div class="row no-gutters">
                <div class="col-md-8 ">
                    <div class="card__post-carousel">
                        @foreach ($heroSlider as $slider)
                        @if ($loop->index <= 4)
                            <div class="item">
                                <!-- Post Article -->
                                <div class="card__post">
                                    <div class="card__post__body">
                                        <a href="{{ route('news-details', $slider->slug) }}">
                                            <img src="{{ asset($slider->image) }}" class="img-fluid" alt="" onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/placeholder.webp') }}';">
                                        </a>
                                        <div class="card__post__content bg__post-cover">
                                            <div class="card__post__category">
                                                {{ $slider->category->name }}
                                            </div>
                                            <div class="card__post__title">
                                                <h2>
                                                    <a href="{{ route('news-details', $slider->slug) }}">
                                                        {!! truncate($slider->title, 100) !!}
                                                    </a>
                                                </h2>
                                            </div>
                                            <div class="card__post__author-info">
                                                <ul class="list-inline">
                                                    <li class="list-inline-item">
                                                        <a href="javascript:;">
                                                            {{ __('frontend.by') }} {{ $slider->auther->name }}
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <span>
                                                            {{ formatDate($slider->created_at) }}
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="popular__news-right">
                        <!-- Post Article -->
                        @if(isset($popularNews) && $popularNews->count() > 0)
                            @foreach ($popularNews as $popularItem)
                            @if ($loop->index < 2)
                            <div class="card__post ">
                                <div class="card__post__body card__post__transition">
                                    <a href="{{ route('news-details', $popularItem->slug) }}">
                                        <img src="{{ asset($popularItem->image) }}" class="img-fluid" alt="" onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/placeholder.webp') }}';">
                                    </a>
                                    <div class="card__post__content bg__post-cover">
                                        <div class="card__post__category">
                                            {{ $popularItem->category->name }}
                                        </div>
                                        <div class="card__post__title">
                                            <h5>
                                                <a href="{{ route('news-details', $popularItem->slug) }}">
                                                    {!! truncate($popularItem->title, 100) !!}
                                                </a>
                                            </h5>
                                        </div>
                                        <div class="card__post__author-info">
                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    <a href="javascript:;">
                                                        {{ __('frontend.by') }} {{ $popularItem->auther->name ?? 'Admin' }}
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <span>
                                                        {{ formatDate($popularItem->created_at) }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @endif
                            @endforeach
                        @else
                            @foreach ($heroSlider as $slider)
                            @if ($loop->index > 4 && $loop->index <= 6)
                            <div class="card__post ">
                                <div class="card__post__body card__post__transition">
                                    <a href="{{ route('news-details', $slider->slug) }}">
                                        <img src="{{ asset($slider->image) }}" class="img-fluid" alt="">
                                    </a>
                                    <div class="card__post__content bg__post-cover">
                                        <div class="card__post__category">
                                            {{ $slider->category->name }}
                                        </div>
                                        <div class="card__post__title">
                                            <h5>
                                                <a href="{{ route('news-details', $slider->slug) }}">
                                                    {!! truncate($slider->title, 100) !!}
                                                </a>
                                            </h5>
                                        </div>
                                        <div class="card__post__author-info">
                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    <a href="javascript:;">
                                                        {{ __('frontend.by') }} {{ $slider->auther->name }}
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <span>
                                                        {{ date('M d, Y', strtotime($slider->created_at)) }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @endif
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Popular news header-->
    <!-- Popular news carousel -->
    {{-- <div class="popular__news-header-carousel">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="top__news__slider">
                        <div class="item">
                            <!-- Post Article -->
                            <div class="article__entry">
                                <div class="article__image">
                                    <a href="#">
                                        <img src="images/newsimage5.png" alt="" class="img-fluid">
                                    </a>
                                </div>
                                <div class="article__content">
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <span class="text-primary">
                                                by david hall
                                            </span>,
                                        </li>

                                        <li class="list-inline-item">
                                            <span>
                                                descember 09, 2016
                                            </span>
                                        </li>
                                    </ul>
                                    <h5>
                                        <a href="#">
                                            Proin eu nisl et arcu iaculis placerat sollicitudin ut est.
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <!-- Post Article -->
                            <div class="article__entry">
                                <div class="article__image">
                                    <a href="#">
                                        <img src="images/newsimage6.png" alt="" class="img-fluid">
                                    </a>
                                </div>
                                <div class="article__content">
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <span class="text-primary">
                                                by david hall
                                            </span>,
                                        </li>

                                        <li class="list-inline-item">
                                            <span>
                                                descember 09, 2016
                                            </span>
                                        </li>
                                    </ul>
                                    <h5>
                                        <a href="#">
                                            Proin eu nisl et arcu iaculis placerat sollicitudin ut est.
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <!-- Post Article -->
                            <div class="article__entry">
                                <div class="article__image">
                                    <a href="#">
                                        <img src="images/newsimage7.png" alt="" class="img-fluid">
                                    </a>
                                </div>
                                <div class="article__content">
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <span class="text-primary">
                                                by david hall
                                            </span>,
                                        </li>

                                        <li class="list-inline-item">
                                            <span>
                                                descember 09, 2016
                                            </span>
                                        </li>
                                    </ul>
                                    <h5>
                                        <a href="#">
                                            Proin eu nisl et arcu iaculis placerat sollicitudin ut est.
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <!-- Post Article -->
                            <div class="article__entry">
                                <div class="article__image">
                                    <a href="#">
                                        <img src="images/newsimage8.png" alt="" class="img-fluid">
                                    </a>
                                </div>
                                <div class="article__content">
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <span class="text-primary">
                                                by david hall
                                            </span>,
                                        </li>

                                        <li class="list-inline-item">
                                            <span>
                                                descember 09, 2016
                                            </span>
                                        </li>
                                    </ul>
                                    <h5>
                                        <a href="#">
                                            Proin eu nisl et arcu iaculis placerat sollicitudin ut est.
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <!-- Post Article -->
                            <div class="article__entry">
                                <div class="article__image">
                                    <a href="#">
                                        <img src="images/newsimage8.png" alt="" class="img-fluid">
                                    </a>
                                </div>
                                <div class="article__content">
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <span class="text-primary">
                                                by david hall
                                            </span>,
                                        </li>

                                        <li class="list-inline-item">
                                            <span>
                                                descember 09, 2016
                                            </span>
                                        </li>
                                    </ul>
                                    <h5>
                                        <a href="#">
                                            Proin eu nisl et arcu iaculis placerat sollicitudin ut est.
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div> --}}
    <!-- End Popular news carousel -->
</section>
