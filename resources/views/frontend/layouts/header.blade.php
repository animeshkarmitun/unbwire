@php
    $languages = \App\Models\Language::where('status', 1)->get();
    $FeaturedCategories = \App\Models\Category::where(['status' => 1, 'language' => getLangauge(), 'show_at_nav' => 1])
        ->whereNull('parent_id')
        ->with('children')
        ->orderByRaw('COALESCE(`order`, 999999) ASC')
        ->orderBy('id', 'asc')
        ->get();

    $categories = \App\Models\Category::where(['status' => 1, 'language' => getLangauge(), 'show_at_nav' => 0])
        ->whereNull('parent_id')
        ->with('children')
        ->orderByRaw('COALESCE(`order`, 999999) ASC')
        ->orderBy('id', 'asc')
        ->get();

@endphp

<header class="bg-light">
    <!-- Navbar  Top-->
    <div class="topbar d-none d-sm-block">
        <div class="container ">
            <div class="row">
                <div class="col-sm-6 col-md-8">
                    <div class="topbar-left topbar-right d-flex">

                        <ul class="topbar-sosmed p-0">
                            @foreach ($socialLinks as $link)
                            <li>
                                <a href="{{ $link->url }}"><i class="{{ $link->icon }}"></i></a>
                            </li>
                            @endforeach

                        </ul>
                        <div class="topbar-text">
                            {{ formatDate(now(), 'full') }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="list-unstyled topbar-right d-flex align-items-center justify-content-end">
                        @if(auth()->check())
                            @php
                                $unreadCount = auth()->user()->unreadNotificationsCount();
                            @endphp
                            <div class="notification-icon-wrapper" style="margin-right: 15px; position: relative;">
                                <a href="{{ route('notifications.index') }}" 
                                   class="notification-link" 
                                   title="{{ $unreadCount > 0 ? $unreadCount . ' unread notifications' : 'No new notifications' }}"
                                   style="color: #333; font-size: 18px; text-decoration: none; position: relative; display: inline-block; transition: color 0.3s ease;"
                                   onmouseover="this.style.color='#dc3545'" 
                                   onmouseout="this.style.color='#333'">
                                    <i class="fas fa-bell"></i>
                                    @if($unreadCount > 0)
                                        <span class="notification-badge" style="position: absolute; top: -8px; right: -8px; background-color: #dc3545; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                    @endif
                                </a>
                            </div>
                        @endif
                        <div class="topbar_language">
                            <select id="site-language">
                                @foreach ($languages as $language)
                                    @php
                                        $displayName = match($language->lang) {
                                            'en' => 'English',
                                            'bn' => 'বাংলা',
                                            default => $language->name,
                                        };
                                        
                                        // Check access permissions
                                        $canShow = true;
                                        if(auth()->check()) {
                                            $package = auth()->user()->currentPackage();
                                            if($package) {
                                                if($language->lang == 'en' && !$package->access_english) $canShow = false;
                                                if(($language->lang == 'bn' || $language->lang == 'bangla') && !$package->access_bangla) $canShow = false;
                                            }
                                        }
                                    @endphp
                                    @if($canShow)
                                    <option value="{{ $language->lang }}" {{ getLangauge() === $language->lang ? 'selected' : '' }}>{{ $displayName }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <ul class="topbar-link">
                            @if (!auth()->check())
                            <li><a href="{{ route('login') }}">{{ __('frontend.Login') }}</a></li>
                            <li><a href="{{ route('register') }}">{{ __('frontend.Register') }}</a></li>
                            @else
                            <li class="dropdown user-dropdown">
                                <a href="javascript:void(0)" class="dropdown-toggle user-dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-circle"></i> <span class="user-name">{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right user-dropdown-menu">
                                    <li>
                                        <a href="{{ route('user.profile') }}" class="dropdown-item">
                                            <i class="fas fa-user" style="margin-right: 8px; width: 16px;"></i> {{ __('frontend.Profile') }}
                                        </a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                            @csrf
                                            <a href="javascript:void(0)" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt" style="margin-right: 8px; width: 16px;"></i> {{ __('frontend.Logout') }}
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Navbar Top  -->


    <!-- Navbar  -->
    <!-- Navbar menu  -->
    <div class="navigation-wrap navigation-shadow bg-white">
        <nav class="navbar navbar-hover navbar-expand-lg navbar-soft">
            <div class="container">
                <div class="offcanvas-header">
                    <div data-toggle="modal" data-target="#modal_aside_right" class="btn-md">
                        <span class="navbar-toggler-icon"></span>
                    </div>
                </div>
                <figure class="mb-0 mx-auto">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset($settings['site_logo']) }}" alt="" class="img-fluid logo">
                    </a>
                </figure>

                <div class="collapse navbar-collapse justify-content-between" id="main_nav99">
                    <ul class="navbar-nav ml-auto ">
                        @foreach ($FeaturedCategories as $category)
                            @if($category->children->count() > 0)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        {{ $category->name }}
                                    </a>
                                    <ul class="dropdown-menu animate fade-up">
                                        <li><a class="dropdown-item" href="{{ route('news', ['category' => $category->slug]) }}">{{ __('frontend.All') }}</a></li>
                                        @foreach ($category->children as $child)
                                            <li><a class="dropdown-item" href="{{ route('news', ['category' => $child->slug]) }}">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link active" href="{{ route('news', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                                </li>
                            @endif
                        @endforeach

                        @if (count($categories) > 0)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"> {{ __('frontend.More') }} </a>
                            <ul class="dropdown-menu animate fade-up">
                                @foreach ($categories as $category)
                                    @if($category->children->count() > 0)
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">{{ $category->name }}</a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('news', ['category' => $category->slug]) }}">{{ __('frontend.All') }}</a></li>
                                                @foreach ($category->children as $child)
                                                    <li><a class="dropdown-item" href="{{ route('news', ['category' => $child->slug]) }}">{{ $child->name }}</a></li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li><a class="dropdown-item icon-arrow" href="{{ route('news', ['category' => $category->slug]) }}"> {{ $category->name }}
                                            </a></li>
                                    @endif
                                @endforeach

                            </ul>
                        </li>
                        @endif

                    </ul>


                    <!-- Search bar.// -->
                    <ul class="navbar-nav ">
                        <li class="nav-item search hidden-xs hidden-sm "> <a class="nav-link" href="#">
                                <i class="fa fa-search"></i>
                            </a>
                        </li>
                    </ul>

                    <!-- Search content bar.// -->
                    <div class="top-search navigation-shadow">
                        <div class="container">
                            <div class="input-group ">
                                <form action="{{ route('news') }}" method="GET">

                                    <div class="row no-gutters mt-3">
                                        <div class="col">
                                            <input class="form-control border-secondary border-right-0 rounded-0"
                                                type="search" value="" placeholder="{{ __('frontend.Search') }}"
                                                id="example-search-input4" name="search">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-outline-secondary border-left-0 rounded-0 rounded-right"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Search content bar.// -->
                </div> <!-- navbar-collapse.// -->
            </div>
        </nav>
    </div>
    <!-- End Navbar menu  -->


    <!-- Navbar sidebar menu  -->
    <div id="modal_aside_right" class="modal fixed-left fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-aside" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="widget__form-search-bar  ">
                        <form action="{{ route('news') }}" method="GET">
                            <div class="row no-gutters">
                                <div class="col">
                                    <input class="form-control border-secondary border-right-0 rounded-0" value=""
                                        placeholder="{{ __('frontend.Search') }}" type="search" name="search">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-outline-secondary border-left-0 rounded-0 rounded-right">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <nav class="list-group list-group-flush">
                        <ul class="navbar-nav ">
                            @foreach ($FeaturedCategories as $category)
                                @if($category->children->count() > 0)
                                    <li class="nav-item">
                                        <a class="nav-link active dropdown-toggle text-dark" href="#" data-toggle="dropdown">{{ $category->name }}</a>
                                        <ul class="dropdown-menu dropdown-menu-left">
                                            <li><a class="dropdown-item" href="{{ route('news', ['category' => $category->slug]) }}">{{ __('frontend.All') }}</a></li>
                                            @foreach ($category->children as $child)
                                                <li><a class="dropdown-item" href="{{ route('news', ['category' => $child->slug]) }}">{{ $child->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a class="nav-link active text-dark" href="{{ route('news', ['category' => $category->slug]) }}"> {{ $category->name }}</a>
                                    </li>
                                @endif
                            @endforeach

                            @if (count($categories) > 0)
                            <li class="nav-item">
                                <a class="nav-link active dropdown-toggle  text-dark" href="#"
                                    data-toggle="dropdown">{{ __('frontend.More') }} </a>
                                <ul class="dropdown-menu dropdown-menu-left">
                                    @foreach ($categories as $category)
                                        @if($category->children->count() > 0)
                                            <li class="dropdown-submenu">
                                                <a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">{{ $category->name }}</a>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('news', ['category' => $category->slug]) }}">{{ __('frontend.All') }}</a></li>
                                                    @foreach ($category->children as $child)
                                                        <li><a class="dropdown-item" href="{{ route('news', ['category' => $child->slug]) }}">{{ $child->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @else
                                            <li><a class="dropdown-item" href="{{ route('news', ['category' => $category->slug]) }}">{{ $category->name }}</a></li>
                                        @endif
                                    @endforeach

                                </ul>
                            </li>
                            @endif

                        </ul>

                    </nav>
                </div>

            </div>
        </div>
    </div>
</header>
