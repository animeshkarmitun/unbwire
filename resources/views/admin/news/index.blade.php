@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.News') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.All News') }}</h4>
                <div class="card-header-action">
                    @if (canAccess(['news create en', 'news create', 'news all-access']))
                        <a href="{{ route('admin.news.create', 'en') }}" class="btn btn-primary mr-2">
                            <i class="fas fa-plus"></i> Create English
                        </a>
                    @endif
                    @if (canAccess(['news create bn', 'news create', 'news all-access']))
                        <a href="{{ route('admin.news.create', 'bn') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Bangla
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('admin.news.index') }}" class="mb-4">
                    <input type="hidden" name="lang" id="filter-lang" value="{{ $selectedLang }}">
                    <input type="hidden" name="preset" id="filter-preset" value="{{ $preset }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                value="{{ $filterStartDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control"
                                value="{{ $filterEndDate }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary mr-2">Search</button>
                            <a href="{{ route('admin.news.index', ['lang' => $selectedLang, 'preset' => 'last_3_days']) }}" class="btn btn-light">
                                Reset
                            </a>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <a href="{{ route('admin.news.index', ['lang' => $selectedLang, 'preset' => 'today']) }}"
                                class="btn btn-sm {{ $preset === 'today' ? 'btn-primary' : 'btn-outline-primary' }} mr-2">Today</a>
                            <a href="{{ route('admin.news.index', ['lang' => $selectedLang, 'preset' => 'last_3_days']) }}"
                                class="btn btn-sm {{ $preset === 'last_3_days' ? 'btn-primary' : 'btn-outline-primary' }} mr-2">Last 3 Days</a>
                            <a href="{{ route('admin.news.index', ['lang' => $selectedLang, 'preset' => 'last_7_days']) }}"
                                class="btn btn-sm {{ $preset === 'last_7_days' ? 'btn-primary' : 'btn-outline-primary' }} mr-2">Last 7 Days</a>
                            <a href="{{ route('admin.news.index', ['lang' => $selectedLang, 'preset' => 'this_month']) }}"
                                class="btn btn-sm {{ $preset === 'this_month' ? 'btn-primary' : 'btn-outline-primary' }}">This Month</a>
                        </div>
                    </div>
                </form>

                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    @foreach ($languages as $language)
                        @php
                            $isActive = false;
                            if (isset($selectedLang) && $selectedLang) {
                                $isActive = ($language->lang === $selectedLang);
                            } else {
                                $isActive = ($loop->index === 0);
                            }
                            
                            // Check if user can view this language's news
                            $canViewLang = canAccess(['news all-access', 'news view', 'news view ' . $language->lang]);
                        @endphp
                        @if($canViewLang)
                        <li class="nav-item">
                            <a class="nav-link {{ $isActive ? 'active' : '' }}" id="home-tab2" data-toggle="tab"
                                href="#home-{{ $language->lang }}" role="tab" aria-controls="home"
                                aria-selected="{{ $isActive ? 'true' : 'false' }}">{{ $language->name }}</a>
                        </li>
                        @endif
                    @endforeach

                </ul>
                <div class="tab-content tab-bordered" id="myTab3Content">
                    @foreach ($languages as $language)
                        @php
                            // Check if user can view this language's news
                            // Permissions are stored as: 'news view', 'news view en', 'news view bn'
                            $canViewLang = canAccess(['news all-access', 'news view', 'news view ' . $language->lang]);
                        @endphp
                        @if($canViewLang)
                        @php
                            $news = $newsByLanguage[$language->lang] ?? collect();
                        @endphp
                        @php
                            $isPaneActive = false;
                            if (isset($selectedLang) && $selectedLang) {
                                $isPaneActive = ($language->lang === $selectedLang);
                            } else {
                                $isPaneActive = ($loop->index === 0);
                            }
                        @endphp
                        <div class="tab-pane fade show {{ $isPaneActive ? 'active' : '' }}"
                            id="home-{{ $language->lang }}" role="tabpanel" aria-labelledby="home-tab2">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-{{ $language->lang }}">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    #
                                                </th>
                                                <th>{{ __('admin.Image') }}</th>
                                                <th>{{ __('admin.Title') }}</th>
                                                <th>{{ __('admin.Category') }}</th>
                                                @if (canAccess(['news status', 'news all-access']))
                                                <th>{{ __('admin.In Breaking') }}</th>
                                                <th>{{ __('admin.In Slider') }}</th>
                                                <th>{{ __('admin.In Popular') }}</th>
                                                @endif
                                                <th>{{ __('admin.Status') }}</th>
                                                <th>Order Position</th>
                                                @if (canAccess(['news all-access']))
                                                <th>Created By</th>
                                                @endif
                                                <th>{{ __('admin.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($news as $item)
                                                <tr>
                                                    <td>{{ $item->id }}</td>
                                                    <td >
                                                        <img src="{{ asset($item->image) }}" width="100" alt="">
                                                    </td>

                                                    <td>{{ $item->title }}</td>
                                                    <td>{{ $item->category->name }}</td>
                                                    @if (canAccess(['news status', 'news all-access']))
                                                        <td>
                                                            <label class="custom-switch mt-2">
                                                                <input {{ $item->is_breaking_news === 1 ? 'checked' : '' }}
                                                                    data-id="{{ $item->id }}" data-name="is_breaking_news"
                                                                    value="1" type="checkbox" class="custom-switch-input toggle-status">
                                                                <span class="custom-switch-indicator"></span>
                                                            </label>
                                                        </td>

                                                        <td>
                                                            <label class="custom-switch mt-2">
                                                                <input {{ $item->show_at_slider === 1 ? 'checked' : '' }}
                                                                    data-id="{{ $item->id }}" data-name="show_at_slider"
                                                                    value="1" type="checkbox" class="custom-switch-input toggle-status">
                                                                <span class="custom-switch-indicator"></span>
                                                            </label>
                                                        </td>

                                                        <td>
                                                            <label class="custom-switch mt-2">
                                                                <input {{ $item->show_at_popular === 1 ? 'checked' : '' }}
                                                                    data-id="{{ $item->id }}" data-name="show_at_popular"
                                                                    value="1" type="checkbox" class="custom-switch-input toggle-status">
                                                                <span class="custom-switch-indicator"></span>
                                                            </label>
                                                        </td>
                                                    @endif

                                                    <td>
                                                        <label class="custom-switch mt-2">
                                                            <input {{ $item->status === 1 ? 'checked' : '' }}
                                                                data-id="{{ $item->id }}" data-name="status"
                                                                value="1" type="checkbox" class="custom-switch-input toggle-status">
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                            class="form-control order-position-input" 
                                                            value="{{ $item->order_position ?? 0 }}" 
                                                            data-id="{{ $item->id }}"
                                                            min="0"
                                                            style="width: 80px; display: inline-block;">
                                                    </td>
                                                    @if (canAccess(['news all-access']))
                                                    <td>
                                                        @if($item->createdByUser)
                                                            @php
                                                                $creator = $item->createdByUser;
                                                                $roleName = null;
                                                                if ($item->created_by_type === 'admin' && $creator instanceof \App\Models\Admin && method_exists($creator, 'getRoleNames')) {
                                                                    try {
                                                                        $roleName = $creator->getRoleNames()->first();
                                                                    } catch (\Exception $e) {
                                                                        // Role not available
                                                                    }
                                                                }
                                                            @endphp
                                                            @if($roleName)
                                                                <strong>{{ $roleName }}</strong>
                                                                <br><small class="text-muted">{{ $creator->name }} ({{ $item->created_by_type }})</small>
                                                            @else
                                                                {{ $creator->name }}
                                                                <br><small class="text-muted">({{ $item->created_by_type }})</small>
                                                            @endif
                                                        @elseif($item->created_by)
                                                            <small class="text-muted">ID: {{ $item->created_by }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    @endif
                                                    <td>
                                                        <a href="{{ route('admin.news.show', $item->id) }}" class="btn btn-success mr-1" title="View News"><i class="fas fa-eye"></i></a>
                                                        <a href="{{ route('admin.news.edit', $item->id) }}"
                                                            class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                                        <a href="{{ route('admin.news.destroy', $item->id) }}"
                                                            class="btn btn-danger delete-item"><i
                                                                class="fas fa-trash-alt"></i></a>
                                                        <a href="{{ route('admin.news-copy', $item->id) }}"
                                                            class="btn btn-primary"
                                                            onclick="return confirm('Are you sure you want to duplicate this news article?')"
                                                            title="Copy News">
                                                            <i class="fas fa-copy"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach

                </div>
            </div>


        </div>
    </section>
@endsection

@push('scripts')
    <script>
        @foreach ($languages as $language)
            $("#table-{{ $language->lang }}").dataTable({
                "columnDefs": [
                    {
                        "sortable": false,
                        "targets": [1, -1] // Disable sorting for Image column (1) and Action column (last)
                    }
                ],
                "order": [
                    [0, 'desc']
                ]
            });
        @endforeach

        $(document).ready(function(){
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                let target = $(e.target).attr('href') || '';
                let selectedLang = target.replace('#home-', '');
                $('#filter-lang').val(selectedLang);
            });

            // Switch to the correct language tab if lang parameter is present
            @if(isset($selectedLang) && $selectedLang)
                var selectedLang = '{{ $selectedLang }}';
                var $tabLink = $('a[href="#home-' + selectedLang + '"]');
                if ($tabLink.length) {
                    $tabLink.tab('show');
                    $('#filter-lang').val(selectedLang);
                }
            @else
                var initialLang = $('.nav-tabs a.active').attr('href');
                if (initialLang) {
                    $('#filter-lang').val(initialLang.replace('#home-', ''));
                }
            @endif

            $('.toggle-status').on('click', function(){
                let id = $(this).data('id');
                let name = $(this).data('name');
                let status = $(this).prop('checked') ? 1 : 0;

                $.ajax({
                    method: 'GET',
                    url: "{{ route('admin.toggle-news-status') }}",
                    data: {
                        id:id,
                        name:name,
                        status:status
                    },
                    success: function(data){
                        if(data.status === 'success'){
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            })
                        }
                    },
                    error: function(error){
                        console.log(error);
                    }
                })
            })

            // Handle order position update on blur (when user leaves the input field)
            $(document).on('blur', '.order-position-input', function(){
                let $input = $(this);
                let id = $input.data('id');
                let orderPosition = parseInt($input.val()) || 0;

                // Validate input
                if(orderPosition < 0){
                    orderPosition = 0;
                    $input.val(0);
                }

                $.ajax({
                    method: 'POST',
                    url: "{{ route('admin.update-news-order-position') }}",
                    data: {
                        id: id,
                        order_position: orderPosition,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data){
                        if(data.status === 'success'){
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            })
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: data.message || 'Failed to update order position'
                            })
                        }
                    },
                    error: function(xhr){
                        let errorMessage = 'Failed to update order position';
                        if(xhr.responseJSON && xhr.responseJSON.message){
                            errorMessage = xhr.responseJSON.message;
                        }
                        Toast.fire({
                            icon: 'error',
                            title: errorMessage
                        })
                    }
                })
            })
        })
    </script>
@endpush
