@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>News Sorting</h1>
        </div>

        <div class="row">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>Sort News by Type</h4>
                    </div>

                    <div class="card-body">
                        <!-- Language Tabs -->
                        <ul class="nav nav-tabs" id="languageTab" role="tablist">
                            @foreach ($languages as $language)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->index === 0 ? 'active' : '' }}" 
                                       id="lang-{{ $language->lang }}-tab" 
                                       data-toggle="tab" 
                                       href="#lang-{{ $language->lang }}" 
                                       role="tab"
                                       data-lang="{{ $language->lang }}">
                                        {{ $language->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-4" id="languageTabContent">
                            @foreach ($languages as $language)
                                <div class="tab-pane fade {{ $loop->index === 0 ? 'show active' : '' }}" 
                                     id="lang-{{ $language->lang }}" 
                                     role="tabpanel"
                                     data-lang="{{ $language->lang }}">
                                    
                                    <!-- News Type Tabs -->
                                    <ul class="nav nav-pills mb-3" id="newsTypeTab-{{ $language->lang }}" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" 
                                               id="breaking-{{ $language->lang }}-tab" 
                                               data-toggle="tab" 
                                               href="#breaking-{{ $language->lang }}" 
                                               role="tab"
                                               data-type="breaking"
                                               data-lang="{{ $language->lang }}">
                                                Breaking News
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" 
                                               id="slider-{{ $language->lang }}-tab" 
                                               data-toggle="tab" 
                                               href="#slider-{{ $language->lang }}" 
                                               role="tab"
                                               data-type="slider"
                                               data-lang="{{ $language->lang }}">
                                                Slider News
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" 
                                               id="popular-{{ $language->lang }}-tab" 
                                               data-toggle="tab" 
                                               href="#popular-{{ $language->lang }}" 
                                               role="tab"
                                               data-type="popular"
                                               data-lang="{{ $language->lang }}">
                                                Popular News
                                            </a>
                                        </li>
                                    </ul>

                                    <!-- News Type Tab Content -->
                                    <div class="tab-content" id="newsTypeContent-{{ $language->lang }}">
                                        <div class="tab-pane fade show active" 
                                             id="breaking-{{ $language->lang }}" 
                                             role="tabpanel"
                                             data-type="breaking">
                                            <div class="sortable-list" 
                                                 id="sortable-breaking-{{ $language->lang }}"
                                                 data-type="breaking"
                                                 data-lang="{{ $language->lang }}">
                                                <div class="text-center py-5">
                                                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                                                    <p class="mt-2">Loading...</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" 
                                             id="slider-{{ $language->lang }}" 
                                             role="tabpanel"
                                             data-type="slider">
                                            <div class="sortable-list" 
                                                 id="sortable-slider-{{ $language->lang }}"
                                                 data-type="slider"
                                                 data-lang="{{ $language->lang }}">
                                                <div class="text-center py-5">
                                                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                                                    <p class="mt-2">Loading...</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" 
                                             id="popular-{{ $language->lang }}" 
                                             role="tabpanel"
                                             data-type="popular">
                                            <div class="sortable-list" 
                                                 id="sortable-popular-{{ $language->lang }}"
                                                 data-type="popular"
                                                 data-lang="{{ $language->lang }}">
                                                <div class="text-center py-5">
                                                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                                                    <p class="mt-2">Loading...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest News Sidebar -->
            <div class="col-lg-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>Latest News</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Select Language</label>
                            <select class="form-control" id="latestNewsLanguage">
                                @foreach ($languages as $language)
                                    <option value="{{ $language->lang }}" {{ $loop->index === 0 ? 'selected' : '' }}>
                                        {{ $language->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Number of News</label>
                            <select class="form-control" id="latestNewsLimit">
                                <option value="10">10</option>
                                <option value="20" selected>20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Search News</label>
                            <input type="text" class="form-control" id="latestNewsSearch" placeholder="Search by title or category...">
                        </div>
                        <div id="latestNewsList">
                            <div class="text-center py-5">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
    <style>
        .sortable-list {
            min-height: 200px;
        }
        .sortable-list > * {
            margin-bottom: 15px !important;
        }
        .sortable-item {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px !important;
            cursor: move;
            transition: all 0.3s;
            display: block;
            width: 100%;
        }
        .sortable-list > .sortable-item {
            margin-bottom: 15px !important;
        }
        .sortable-list .sortable-item:not(:last-child) {
            margin-bottom: 15px !important;
        }
        .sortable-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .sortable-item.sortable-ghost {
            opacity: 0.4;
        }
        .sortable-item .news-header {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 12px !important;
            width: 100% !important;
            flex-wrap: nowrap !important;
        }
        .sortable-item > .news-header {
            display: flex !important;
            flex-direction: row !important;
        }
        .sortable-item .news-image {
            width: 60px !important;
            height: 60px !important;
            max-width: 60px !important;
            max-height: 60px !important;
            min-width: 60px !important;
            min-height: 60px !important;
            object-fit: cover;
            border-radius: 4px;
            flex-shrink: 0 !important;
            display: block !important;
            float: none !important;
            margin-right: 15px !important;
        }
        .sortable-item img.news-image {
            width: 60px !important;
            height: 60px !important;
            max-width: 60px !important;
            max-height: 60px !important;
            display: block;
            margin-right: 15px !important;
        }
        .sortable-item .news-info {
            flex: 1 1 auto !important;
            min-width: 0;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center;
            flex-shrink: 1;
        }
        .sortable-item .news-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
            word-wrap: break-word;
        }
        .sortable-item .news-meta {
            font-size: 12px;
            color: #6c757d;
        }
        .sortable-item .news-actions {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 5px;
            flex-shrink: 0 !important;
            margin-left: auto;
        }
        .latest-news-item {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 15px !important;
        }
        #latestNewsList > .latest-news-item {
            margin-bottom: 15px !important;
        }
        #latestNewsList .latest-news-item:not(:last-child) {
            margin-bottom: 15px !important;
        }
        .latest-news-item .news-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .latest-news-item .news-info {
            flex: 1;
        }
        .latest-news-item .news-info {
            flex: 1;
        }
        .latest-news-item .news-title {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 3px;
            color: #333;
        }
        .latest-news-item .news-meta {
            font-size: 11px;
            color: #6c757d;
        }
        .latest-news-item .news-actions {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .btn-sm {
            padding: 2px 8px;
            font-size: 11px;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            let sortableInstances = {};
            let currentLanguage = $('#languageTab .nav-link.active').data('lang') || '{{ $languages->first()->lang }}';
            let isSyncingLanguage = false; // Flag to prevent infinite loop

            // Initialize latest news
            loadLatestNews();

            // Load news when language tab changes
            $('#languageTab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if (isSyncingLanguage) return; // Prevent loop when syncing from sidebar
                currentLanguage = $(e.target).data('lang');
                // Sync sidebar language dropdown
                isSyncingLanguage = true;
                $('#latestNewsLanguage').val(currentLanguage);
                isSyncingLanguage = false;
                loadLatestNews();
                loadNewsForAllTypes(currentLanguage);
            });

            // Load news when news type tab changes
            $('[id^="newsTypeTab-"] a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const type = $(e.target).data('type');
                const lang = $(e.target).data('lang');
                loadNewsByType(type, lang);
            });

            // Latest news language change - sync main language tabs
            $('#latestNewsLanguage').on('change', function() {
                if (isSyncingLanguage) return; // Prevent loop when syncing from main tabs
                const selectedLang = $(this).val();
                currentLanguage = selectedLang;
                // Find and activate the corresponding language tab
                isSyncingLanguage = true;
                $('#languageTab a[data-lang="' + selectedLang + '"]').tab('show');
                isSyncingLanguage = false;
                loadLatestNews();
            });

            // Latest news limit change
            $('#latestNewsLimit').on('change', function() {
                loadLatestNews();
            });

            // Latest news search (with debounce)
            let searchTimeout;
            $('#latestNewsSearch').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val();
                searchTimeout = setTimeout(function() {
                    loadLatestNews();
                }, 500); // Wait 500ms after user stops typing
            });

            // Load news for all types when language changes
            function loadNewsForAllTypes(lang) {
                ['breaking', 'slider', 'popular'].forEach(type => {
                    loadNewsByType(type, lang);
                });
            }

            // Load news by type
            function loadNewsByType(type, lang) {
                const container = $(`#sortable-${type}-${lang}`);
                container.html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>');

                $.ajax({
                    url: "{{ route('admin.news-sorting.get-news', ':type') }}".replace(':type', type),
                    method: 'GET',
                    data: { language: lang },
                    success: function(response) {
                        if (response.status === 'success') {
                            renderNewsList(container, response.data, type, lang);
                            initializeSortable(type, lang);
                        }
                    },
                    error: function() {
                        container.html('<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Failed to load news</p></div>');
                    }
                });
            }

            // Get order for specific tab type
            function getOrderForType(item, type) {
                const orderMap = {
                    'breaking': item.breaking_order || 0,
                    'slider': item.slider_order || 0,
                    'popular': item.popular_order || 0
                };
                return orderMap[type] || item.order_position || 0;
            }

            // Render news list
            function renderNewsList(container, news, type, lang) {
                if (news.length === 0) {
                    container.html('<div class="empty-state"><i class="fas fa-inbox"></i><p>No news found</p></div>');
                    return;
                }

                let html = '';
                news.forEach(function(item, index) {
                    // Get order, fallback to index + 1 if order is 0 or not set
                    let order = getOrderForType(item, type);
                    if (order === 0 || order === null || order === undefined) {
                        order = index + 1;
                    }
                    
                    html += `
                        <div class="sortable-item" data-id="${item.id}" style="margin-bottom: 15px !important;">
                            <div class="news-header" style="display: flex !important; flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; width: 100% !important;">
                                <img src="${item.image ? '{{ asset("") }}' + item.image : '{{ asset("admin/assets/img/placeholder.webp") }}'}" 
                                     alt="${item.title}" 
                                     class="news-image"
                                     style="width: 60px !important; height: 60px !important; max-width: 60px !important; max-height: 60px !important; min-width: 60px !important; min-height: 60px !important; object-fit: cover; border-radius: 4px; flex-shrink: 0 !important; display: block !important; float: none !important; margin-right: 15px !important;"
                                     onerror="this.onerror=null; this.src='{{ asset("admin/assets/img/placeholder.webp") }}'; this.style.width='60px'; this.style.height='60px'; this.style.maxWidth='60px'; this.style.maxHeight='60px'; this.style.marginRight='15px';">
                                <div class="news-info" style="flex: 1 1 auto !important; min-width: 0 !important; display: flex !important; flex-direction: column !important; justify-content: center !important;">
                                    <div class="news-title">${item.title}</div>
                                    <div class="news-meta">
                                        <span>${item.category ? item.category.name : 'N/A'}</span> | 
                                        <span>Order: ${order}</span>
                                    </div>
                                </div>
                                <div class="news-actions" style="display: flex !important; flex-direction: row !important; align-items: center !important; flex-shrink: 0 !important; margin-left: auto !important;">
                                    <button class="btn btn-danger btn-sm remove-from-tab" 
                                            data-id="${item.id}" 
                                            data-type="${type}"
                                            title="Remove from ${type}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.html(html);
            }

            // Initialize SortableJS
            function initializeSortable(type, lang) {
                const container = document.getElementById(`sortable-${type}-${lang}`);
                if (!container) return;

                const instanceKey = `${type}-${lang}`;
                
                // Destroy existing instance if any
                if (sortableInstances[instanceKey]) {
                    sortableInstances[instanceKey].destroy();
                }

                sortableInstances[instanceKey] = new Sortable(container, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        const newsIds = [];
                        container.querySelectorAll('.sortable-item').forEach(function(item) {
                            newsIds.push($(item).data('id'));
                        });
                        
                        updateOrder(newsIds, type);
                    }
                });
            }

            // Update order via AJAX
            function updateOrder(newsIds, type) {
                $.ajax({
                    url: "{{ route('admin.news-sorting.update-order') }}",
                    method: 'POST',
                    data: {
                        news_ids: newsIds,
                        type: type,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Toast.fire({
                                icon: 'success',
                                title: 'Order updated successfully'
                            });
                            // Reload to show updated order positions
                            const lang = currentLanguage;
                            loadNewsByType(type, lang);
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Failed to update order'
                        });
                    }
                });
            }

            // Load latest news
            function loadLatestNews(lang) {
                // If lang is provided, use it; otherwise get from dropdown
                const selectedLang = lang || $('#latestNewsLanguage').val();
                const limit = $('#latestNewsLimit').val();
                const search = $('#latestNewsSearch').val();
                fetchLatestNews(selectedLang, limit, search);
            }

            // Fetch latest news via custom endpoint
            function fetchLatestNews(lang, limit, search) {
                const container = $('#latestNewsList');
                container.html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>');

                const requestData = {
                    language: lang,
                    limit: limit || 20
                };

                // Add search parameter if provided
                if (search && search.trim() !== '') {
                    requestData.search = search.trim();
                }

                $.ajax({
                    url: "{{ route('admin.news-sorting.get-news', 'latest') }}",
                    method: 'GET',
                    data: requestData,
                    success: function(response) {
                        if (response.status === 'success') {
                            renderLatestNews(response.data);
                        } else {
                            container.html('<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Failed to load news</p></div>');
                        }
                    },
                    error: function() {
                        container.html('<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Failed to load news</p></div>');
                    }
                });
            }

            // Render latest news list
            function renderLatestNews(news) {
                const container = $('#latestNewsList');
                
                if (news.length === 0) {
                    container.html('<div class="empty-state"><i class="fas fa-inbox"></i><p>No news available</p></div>');
                    return;
                }

                let html = '';
                news.forEach(function(item) {
                    const isBreaking = item.is_breaking_news ? 1 : 0;
                    const isSlider = item.show_at_slider ? 1 : 0;
                    const isPopular = item.show_at_popular ? 1 : 0;
                    
                    html += `
                        <div class="latest-news-item" data-id="${item.id}" style="margin-bottom: 15px !important;">
                            <div class="news-header">
                                <div class="news-info">
                                    <div class="news-title">${item.title.substring(0, 60)}${item.title.length > 60 ? '...' : ''}</div>
                                    <div class="news-meta">${item.category ? item.category.name : 'N/A'}</div>
                                </div>
                                <div class="news-actions">
                                    ${!isBreaking ? `<button class="btn btn-primary btn-sm add-to-tab" data-id="${item.id}" data-type="breaking" title="Add to Breaking"><i class="fas fa-plus"></i></button>` : ''}
                                    ${!isSlider ? `<button class="btn btn-info btn-sm add-to-tab" data-id="${item.id}" data-type="slider" title="Add to Slider"><i class="fas fa-plus"></i></button>` : ''}
                                    ${!isPopular ? `<button class="btn btn-success btn-sm add-to-tab" data-id="${item.id}" data-type="popular" title="Add to Popular"><i class="fas fa-plus"></i></button>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.html(html);
            }

            // Add news to tab
            $(document).on('click', '.add-to-tab', function() {
                const newsId = $(this).data('id');
                const type = $(this).data('type');
                const lang = currentLanguage;

                $.ajax({
                    url: "{{ route('admin.news-sorting.add-to-tab') }}",
                    method: 'POST',
                    data: {
                        news_id: newsId,
                        type: type,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Toast.fire({
                                icon: 'success',
                                title: 'News added successfully'
                            });
                            loadNewsByType(type, lang);
                            loadLatestNews();
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to add news';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Toast.fire({
                            icon: 'error',
                            title: message
                        });
                    }
                });
            });

            // Remove news from tab
            $(document).on('click', '.remove-from-tab', function() {
                const newsId = $(this).data('id');
                const type = $(this).data('type');
                const lang = currentLanguage;

                if (!confirm('Are you sure you want to remove this news from ' + type + '?')) {
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.news-sorting.remove-from-tab') }}",
                    method: 'POST',
                    data: {
                        news_id: newsId,
                        type: type,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Toast.fire({
                                icon: 'success',
                                title: 'News removed successfully'
                            });
                            loadNewsByType(type, lang);
                            loadLatestNews();
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to remove news';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Toast.fire({
                            icon: 'error',
                            title: message
                        });
                    }
                });
            });

            // Initialize on page load
            loadNewsForAllTypes(currentLanguage);
        });
    </script>
@endpush

