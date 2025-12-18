@php
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
    $settings = array_merge([
        'site_seo_title' => config('app.name', 'Laravel'),
        'site_seo_description' => '',
        'site_seo_keywords' => '',
        'site_logo' => 'frontend/assets/images/logo.png',
        'site_favicon' => 'frontend/assets/images/favicon.png',
        'site_color' => '#ff5733',
    ], $settings);
@endphp

<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@hasSection('title') @yield('title') @else {{ $settings['site_seo_title'] }} @endif </title>
    <meta name="description" content="@hasSection('meta_description') @yield('meta_description') @else {{ $settings['site_seo_description'] }} @endif " />
    <meta name="keywords" content="{{ $settings['site_seo_keywords'] }}" />

    <meta name="og:title" content="@yield('meta_og_title')" />
    <meta name="og:description" content="@yield('meta_og_description')" />
    <meta name="og:image" content="@hasSection('meta_og_image') @yield('meta_og_image') @else {{ asset($settings['site_logo']) }} @endif" />
    <meta name="twitter:title" content="@yield('meta_tw_title')" />
    <meta name="twitter:description" content="@yield('meta_tw_description')" />
    <meta name="twitter:image" content="@yield('meta_tw_image')" />

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset($settings['site_favicon']) }}" type="image/png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link href="{{ asset('frontend/assets/css/styles.css') }}" rel="stylesheet">
    <style>
        :root {
            --colorPrimary: {{ $settings['site_color'] }};
        }
        
        /* Modern User Dropdown Styling */
        .user-dropdown .user-dropdown-toggle {
            color: #fff !important;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }
        
        .user-dropdown .user-dropdown-toggle:hover,
        .user-dropdown .user-dropdown-toggle:focus {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff !important;
            text-decoration: none;
            outline: none;
        }
        
        .user-dropdown .user-dropdown-toggle i {
            font-size: 16px;
            vertical-align: middle;
        }
        
        .user-dropdown .user-dropdown-toggle .user-name {
            display: inline-block;
            vertical-align: middle;
        }
        
        /* Hide default Bootstrap caret and add custom one */
        .user-dropdown .user-dropdown-toggle::after {
            content: "\f107";
            font-family: "FontAwesome";
            font-weight: 900;
            border: none;
            margin-left: 8px;
            vertical-align: middle;
            font-size: 12px;
        }
        
        .user-dropdown.show .user-dropdown-toggle::after {
            content: "\f106";
        }
        
        .user-dropdown-menu {
            min-width: 200px;
            background-color: #fff;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 8px 0;
            margin-top: 8px !important;
            margin-right: 0 !important;
        }
        
        .user-dropdown-menu .dropdown-item {
            color: #333 !important;
            padding: 10px 20px;
            font-size: 14px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            border: none;
        }
        
        .user-dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--colorPrimary) !important;
            padding-left: 24px;
        }
        
        .user-dropdown-menu .dropdown-item i {
            color: #666;
            transition: color 0.2s ease;
            width: 20px;
            text-align: center;
            margin-right: 10px;
            flex-shrink: 0;
        }
        
        .user-dropdown-menu .dropdown-item span {
            flex: 1;
        }
        
        .user-dropdown-menu .dropdown-item:hover i {
            color: var(--colorPrimary);
        }
        
        .user-dropdown-menu .divider {
            height: 1px;
            margin: 8px 0;
            overflow: hidden;
            background-color: #e9ecef;
            border: none;
        }
        
        @media (max-width: 768px) {
            .user-dropdown-menu {
                margin-right: 10px !important;
            }
        }
    </style>
</head>

<body>

    <!-- Global Variables -->
    @php
        $socialLinks = \App\Models\SocialLink::where('status', 1)->get();
        $footerInfo = \App\Models\FooterInfo::where('language', getLangauge())->first();
        $footerGridOne = \App\Models\FooterGridOne::where(['status' => 1, 'language' => getLangauge()])->get();
        $footerGridTwo = \App\Models\FooterGridTwo::where(['status' => 1, 'language' => getLangauge()])->get();
        $footerGridThree = \App\Models\FooterGridThree::where(['status' => 1, 'language' => getLangauge()])->get();
        $footerGridOneTitle = \App\Models\FooterTitle::where(['key' => 'grid_one_title', 'language' => getLangauge()])->first();
        $footerGridTwoTitle = \App\Models\FooterTitle::where(['key' => 'grid_two_title', 'language' => getLangauge()])->first();
        $footerGridThreeTitle = \App\Models\FooterTitle::where(['key' => 'grid_three_title', 'language' => getLangauge()])->first();
    @endphp

    <!-- Header news -->
    @include('frontend.layouts.header')
    <!-- End Header news -->

    @yield('content')

    <!-- Footer Section -->
    @include('frontend.layouts.footer')
    <!-- End Footer Section -->


    <a href="javascript:" id="return-to-top"><i class="fa fa-chevron-up"></i></a>

    <script type="text/javascript" src="{{ asset('frontend/assets/js/index.bundle.js') }}"></script>
    @if (\Illuminate\Support\Facades\View::exists('sweetalert::alert'))
        @include('sweetalert::alert')
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })


        // Add csrf token in ajax request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            /** change language **/
            $('#site-language').on('change', function() {
                let languageCode = $(this).val();
                $.ajax({
                    method: 'POST',
                    url: "{{ route('language') }}",
                    data: {
                        language_code: languageCode
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            window.location.href = "{{ url('/') }}";
                        }
                    },
                    error: function(data) {
                        console.error(data);
                    }
                })
            })

            /** Subscribe Newsletter**/
            $('.newsletter-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    method: 'POST',
                    url: "{{ route('subscribe-newsletter') }}",
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('.newsletter-button').text('{{ __('frontend.loading...') }}');
                        $('.newsletter-button').attr('disabled', true);
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            })
                            $('.newsletter-form')[0].reset();
                            $('.newsletter-button').text('{{ __('frontend.sign up') }}');

                            $('.newsletter-button').attr('disabled', false);
                        }
                    },
                    error: function(data) {
                        $('.newsletter-button').text('{{ __('frontend.sign up') }}');
                        $('.newsletter-button').attr('disabled', false);

                        if (data.status === 422) {
                            let errors = data.responseJSON.errors;
                            $.each(errors, function(index, value) {
                                Toast.fire({
                                    icon: 'error',
                                    title: value[0]
                                })
                            })
                        }
                    }
                })
            })
        })
    </script>



    @stack('content')

</body>

</html>
