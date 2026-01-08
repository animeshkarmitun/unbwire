<div class="navbar-bg"></div>
<!-- Navbar Start -->
@include('admin.layouts.navbar')
<!-- Navbar End -->

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center justify-content-center">
                @php
                    $siteLogo = getSetting('site_logo') ?? 'frontend/assets/images/logo1.png';
                @endphp
                @if(file_exists(public_path($siteLogo)))
                    <img src="{{ asset($siteLogo) }}" alt="UNBNEWS" class="p-2" style="max-height: 70px; width: auto;">
                @else
                    <span>{{ __('admin.Stisla') }}</span>
                @endif
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center justify-content-center">
                @php
                    $siteLogo = getSetting('site_logo') ?? 'frontend/assets/images/logo1.png';
                @endphp
                @if(file_exists(public_path($siteLogo)))
                    <img src="{{ asset($siteLogo) }}" alt="UNB" style="max-height: 35px; width: auto;">
                @else
                    <span>{{ __('admin.St') }}</span>
                @endif
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">{{ __('admin.Dashboard') }}</li>
            <li class="active">
                <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="fas fa-fire"></i><span>{{ __('admin.Dashboard') }}</span></a>
            </li>
            
            @if (canAccess(['analytics index']))
                <li class="{{ setSidebarActive(['admin.analytics.*']) }}">
                    <a href="{{ route('admin.analytics.index') }}" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytics</span>
                    </a>
                </li>
            @endif
            
            @if (canAccess(['activity log index']))
                <li class="{{ setSidebarActive(['admin.activity-log.*']) }}">
                    <a href="{{ route('admin.activity-log.index') }}" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span>Activity Logs</span>
                    </a>
                </li>
            @endif
            
            <li class="menu-header">{{ __('admin.Starter') }}</li>

            @if (canAccess(['category index', 'category create', 'category udpate', 'category delete']))
                <li class="{{ setSidebarActive(['admin.category.*']) }}"><a class="nav-link"
                        href="{{ route('admin.category.index') }}"><i class="fas fa-list"></i>
                        <span>{{ __('admin.Category') }}</span></a></li>
            @endif

            @if (canAccess(['news index', 'news create', 'news update', 'news delete']))
                <li class="{{ setSidebarActive(['admin.author.*']) }}"><a class="nav-link"
                        href="{{ route('admin.author.index') }}"><i class="fas fa-user-edit"></i>
                        <span>Manage Author</span></a></li>
            @endif

            @if (canAccess(['news index']))
                <li class="dropdown {{ setSidebarActive(['admin.news.*', 'admin.pending.news', 'admin.news-sorting.*']) }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-newspaper"></i>
                        <span>{{ __('admin.News') }}</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.news.*']) }}"><a class="nav-link"
                                href="{{ route('admin.news.index') }}">{{ __('admin.All News') }}</a></li>

                        <li class="{{ setSidebarActive(['admin.pending.news']) }}"><a class="nav-link"
                                href="{{ route('admin.pending.news') }}">{{ __('admin.Pending News') }}</a></li>

                        <li class="{{ setSidebarActive(['admin.news-sorting.*']) }}"><a class="nav-link"
                                href="{{ route('admin.news-sorting.index') }}">News Sorting</a></li>

                    </ul>
                </li>
            @endif

            @if (canAccess(['about index', 'contact index']))
                <li class="dropdown {{ setSidebarActive(['admin.about.*', 'admin.contact.*']) }}">
                    <a href="#" class="nav-link has-dropdown"><i class="far fa-file-alt"></i>
                        <span>{{ __('admin.Pages') }}</span></a>
                    <ul class="dropdown-menu">
                        @if (canAccess(['about index']))
                            <li class="{{ setSidebarActive(['admin.about.*']) }}"><a class="nav-link"
                                    href="{{ route('admin.about.index') }}">{{ __('admin.About Page') }}</a></li>
                        @endif
                        @if (canAccess(['conatact index']))
                            <li class="{{ setSidebarActive(['admin.contact.*']) }}"><a class="nav-link"
                                    href="{{ route('admin.contact.index') }}">{{ __('admin.Contact Page') }}</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (canAccess(['social media index']))
                <li class="{{ setSidebarActive(['admin.social-count.*']) }}"><a class="nav-link"
                        href="{{ route('admin.social-count.index') }}"><i class="fas fa-hashtag"></i>
                        <span>{{ __('admin.Social Media') }}</span></a></li>
            @endif

            @if (canAccess(['contact message index']))
                <li class="{{ setSidebarActive(['admin.contact-message.*']) }}"><a class="nav-link"
                        href="{{ route('admin.contact-message.index') }}"><i class="fas fa-id-card-alt"></i>
                        <span>{{ __('admin.Contact Messages') }} </span>
                        @if ($unReadMessages > 0)
                            <i class="badge bg-danger" style="color:
            #fff">{{ $unReadMessages }}</i>
                        @endif
                    </a></li>
            @endif

            @if (canAccess(['support tickets index']))
                <li class="dropdown {{ setSidebarActive(['admin.support-tickets.*', 'admin.support-ticket-tags.*']) }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i>
                        <span>Support Tickets</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.support-tickets.*']) }}"><a class="nav-link"
                                href="{{ route('admin.support-tickets.index') }}">All Tickets</a></li>
                        <li class="{{ setSidebarActive(['admin.support-ticket-tags.*']) }}"><a class="nav-link"
                                href="{{ route('admin.support-ticket-tags.index') }}">Tags</a></li>
                    </ul>
                </li>
            @endif

            @if (canAccess(['home section index']))
                <li class="{{ setSidebarActive(['admin.home-section-setting.*']) }}"><a class="nav-link"
                        href="{{ route('admin.home-section-setting.index') }}"><i class="fas fa-wrench"></i>
                        <span>{{ __('admin.Home Section Setting') }}</span></a></li>
            @endif

            @if (canAccess(['advertisement index']))
                <li class="{{ setSidebarActive(['admin.ad.*']) }}"><a class="nav-link"
                        href="{{ route('admin.ad.index') }}"><i class="fas fa-ad"></i>
                        <span>{{ __('admin.Advertisement') }}</span></a></li>
            @endif

            @if (canAccess(['media library index']))
                <li class="{{ setSidebarActive(['admin.media-library.*']) }}"><a class="nav-link"
                        href="{{ route('admin.media-library.index') }}"><i class="fas fa-images"></i>
                        <span>{{ __('admin.Media Library') }}</span></a></li>
            @endif

            @if (canAccess(['image gallery index', 'video gallery index']))
                <li class="dropdown {{ setSidebarActive(['admin.image-gallery.*', 'admin.video-gallery.*']) }}">
                    <a href="javascript:void(0)" class="nav-link has-dropdown"><i class="fas fa-photo-video"></i>
                        <span>Gallery</span></a>
                    <ul class="dropdown-menu">
                        @if (canAccess(['image gallery index']))
                            <li class="{{ setSidebarActive(['admin.image-gallery.*']) }}"><a class="nav-link"
                                    href="{{ route('admin.image-gallery.index') }}"><i class="fas fa-image"></i> Images</a></li>
                        @endif
                        @if (canAccess(['video gallery index']))
                            <li class="{{ setSidebarActive(['admin.video-gallery.*']) }}"><a class="nav-link"
                                    href="{{ route('admin.video-gallery.index') }}"><i class="fas fa-video"></i> Videos</a></li>
                        @endif
                    </ul>
                </li>
            @endif


            @if (canAccess(['subscription package index']))
                <li class="dropdown {{ setSidebarActive(['admin.subscription-package.*', 'admin.user-subscription.*']) }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-crown"></i>
                        <span>Subscriptions</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.subscription-package.*']) }}"><a class="nav-link"
                                href="{{ route('admin.subscription-package.index') }}">{{ __('admin.Subscription Packages') }}</a></li>
                        <li class="{{ setSidebarActive(['admin.user-subscription.*']) }}"><a class="nav-link"
                                href="{{ route('admin.user-subscription.index') }}">User Subscriptions</a></li>
                    </ul>
                </li>
            @endif

            @if (canAccess(['subscribers index']) || canAccess(['setting index']))
                <li class="dropdown {{ setSidebarActive(['admin.subscriber.*', 'admin.subscriber-notification-settings.*', 'admin.email-report.*']) }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-bell"></i>
                        <span>Notifications & Email</span></a>
                    <ul class="dropdown-menu">
                        @if (canAccess(['subscribers index']))
                            <li class="{{ setSidebarActive(['admin.subscriber.*']) }}"><a class="nav-link"
                                    href="{{ route('admin.subscriber.index') }}"><i class="fas fa-users"></i> Subscribers</a></li>
                        @endif
                        @if (canAccess(['setting index']))
                            <li class="{{ setSidebarActive(['admin.subscriber-notification-settings.*']) }}"><a class="nav-link"
                                    href="{{ route('admin.subscriber-notification-settings.index') }}"><i class="fas fa-cog"></i> Settings</a></li>
                        @endif
                        @if (canAccess(['subscribers index']))
                            <li class="{{ setSidebarActive(['admin.email-report.*']) }}"><a class="nav-link"
                                    href="{{ route('admin.email-report.index') }}"><i class="fas fa-envelope"></i> Email Reports</a></li>
                            <li class="{{ setSidebarActive(['admin.email-report.pending']) }}"><a class="nav-link"
                                    href="{{ route('admin.email-report.pending') }}"><i class="fas fa-clock"></i> Pending Emails</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (canAccess(['footer index']))
                <li
                    class="dropdown
                {{ setSidebarActive([
                    'admin.social-link.*',
                    'admin.footer-info.*',
                    'admin.footer-grid-one.*',
                    'admin.footer-grid-three.*',
                    'admin.footer-grid-two.*'
                ]) }}
            ">
                    <a href="#" class="nav-link has-dropdown"><i class="far fa-file-alt"></i>
                        <span>{{ __('admin.Footer') }} {{ __('admin.Setting') }}</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setSidebarActive(['admin.social-link.*']) }}"><a class="nav-link"
                                href="{{ route('admin.social-link.index') }}">{{ __('admin.Social Links') }}</a></li>
                        <li class="{{ setSidebarActive(['admin.footer-info.*']) }}"><a class="nav-link"
                                href="{{ route('admin.footer-info.index') }}">{{ __('admin.Footer Info') }}</a></li>
                        <li class="{{ setSidebarActive(['admin.footer-grid-one.*']) }}"><a class="nav-link"
                                href="{{ route('admin.footer-grid-one.index') }}">{{ __('admin.Footer Grid One') }}</a></li>
                        <li class="{{ setSidebarActive(['admin.footer-grid-two.*']) }}"><a class="nav-link"
                                href="{{ route('admin.footer-grid-two.index') }}">{{ __('admin.Footer Grid Two') }}</a></li>
                        <li class="{{ setSidebarActive(['admin.footer-grid-three.*']) }}"><a class="nav-link"
                                href="{{ route('admin.footer-grid-three.index') }}">{{ __('admin.Footer Grid Three') }}</a>
                        </li>

                    </ul>
                </li>
            @endif

            @if (canAccess(['access management index']))
                <li class="dropdown
                {{ setSidebarActive([
                    'admin.role.*',
                    'admin.role-users.*'
                    ]) }}
            ">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-shield"></i>
                        <span>{{ __('admin.Access Management') }}</span></a>
                    <ul class="dropdown-menu">

                        <li class="{{ setSidebarActive(['admin.role-users.*']) }}"><a class="nav-link"
                                href="{{ route('admin.role-users.index') }}">{{ __('admin.Role Users') }}</a></li>

                        <li class="{{ setSidebarActive(['admin.role.*']) }}"><a class="nav-link"
                                href="{{ route('admin.role.index') }}">{{ __('admin.Roles and Permissions') }}</a></li>
                    </ul>
                </li>
            @endif

            @if (canAccess(['setting index']))
                <li class="{{ setSidebarActive(['admin.setting.*']) }}"><a class="nav-link"
                        href="{{ route('admin.setting.index') }}"><i class="fas fa-cog"></i>
                        <span>{{ __('admin.Settings') }}</span></a></li>
            @endif

            @if (canAccess(['languages index']))

            <li class="dropdown
                {{ setSidebarActive([
                    'admin.frontend-localization.index',
                    'admin.admin-localization.index',
                    'admin.language.*'
                ]) }}
            ">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-language"></i>
                    <span>{{ __('admin.Localization') }}</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ setSidebarActive(['admin.language.*']) }}"><a class="nav-link"
                        href="{{ route('admin.language.index') }}">
                        <span>{{ __('admin.Languages') }}</span></a></li>

                    <li class="{{ setSidebarActive(['admin.frontend-localization.index']) }}"><a class="nav-link"
                        href="{{ route('admin.frontend-localization.index') }}">
                        <span>{{ __('admin.Frontend Lang') }}</span></a></li>

                    <li class="{{ setSidebarActive(['admin.admin-localization.index']) }}"><a class="nav-link"
                        href="{{ route('admin.admin-localization.index') }}">
                        <span>{{ __('admin.Admin Lang') }}</span></a></li>
                </ul>
            </li>
            @endif

            {{-- <li><a class="nav-link" href="blank.html"><i class="far fa-square"></i> <span>Blank Page</span></a></li> --}}

            {{-- <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="far fa-file-alt"></i> <span>Forms</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="forms-advanced-form.html">Advanced Form</a></li>
                    <li><a class="nav-link" href="forms-editor.html">Editor</a></li>
                    <li><a class="nav-link" href="forms-validation.html">Validation</a></li>
                </ul>
            </li> --}}

        </ul>
    </aside>
</div>
