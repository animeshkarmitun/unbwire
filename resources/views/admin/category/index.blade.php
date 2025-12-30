@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Categories') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.All Categories') }}</h4>
                <div class="card-header-action">
                    @if (canAccess(['category create en', 'category create', 'news all-access']))
                        <a href="{{ route('admin.category.create', 'en') }}" class="btn btn-primary mr-2">
                            <i class="fas fa-plus"></i> Create English
                        </a>
                    @endif
                    @if (canAccess(['category create bn', 'category create', 'news all-access']))
                        <a href="{{ route('admin.category.create', 'bn') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Bangla
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    @foreach ($languages as $language)
                        @php
                            // Check if user can view this language's categories
                            $canViewLang = canAccess(['news all-access', 'category view', 'category view ' . $language->lang]);
                        @endphp
                        @if($canViewLang)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->index === 0 ? 'active' : '' }}" id="home-tab2" data-toggle="tab"
                                href="#home-{{ $language->lang }}" role="tab" aria-controls="home"
                                aria-selected="true">{{ $language->name }}</a>
                        </li>
                        @endif
                    @endforeach

                </ul>
                <div class="tab-content tab-bordered" id="myTab3Content">
                    @foreach ($languages as $language)
                        @php
                            // Check if user can view this language's categories
                            $canViewLang = canAccess(['news all-access', 'category view', 'category view ' . $language->lang]);
                        @endphp
                        @if($canViewLang)
                        @php
                            // Double-check permission before loading categories
                            $canViewLang = canAccess(['news all-access', 'category view', 'category view ' . $language->lang]);
                            if ($canViewLang) {
                                $categories = \App\Models\Category::where('language', $language->lang)
                                    ->with('parent', 'children')
                                    ->orderByRaw('COALESCE(`parent_id`, 0) ASC')
                                    ->orderBy('order', 'asc')
                                    ->orderBy('id', 'desc')
                                    ->get();
                            } else {
                                $categories = collect(); // Empty collection if no permission
                            }
                        @endphp
                        <div class="tab-pane fade show {{ $loop->index === 0 ? 'active' : '' }}"
                            id="home-{{ $language->lang }}" role="tabpanel" aria-labelledby="home-tab2">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-{{ $language->lang }}">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    #
                                                </th>
                                                <th>{{ __('admin.Name') }}</th>
                                                <th>{{ __('Parent Category') }}</th>
                                                <th>{{ __('admin.Language Code') }}</th>
                                                <th>Menu Order</th>
                                                <th>{{ __('admin.In Nav') }}</th>
                                                <th>{{ __('admin.Status') }}</th>
                                                <th>{{ __('admin.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $category)
                                                <tr>
                                                    <td>{{ $category->id }}</td>
                                                    <td>
                                                        @if($category->parent_id)
                                                            <i class="fas fa-level-up-alt text-muted mr-1" style="transform: rotate(90deg);"></i>
                                                        @endif
                                                        {{ $category->name }}
                                                        @if($category->hasChildren())
                                                            <span class="badge badge-info ml-1">{{ $category->children->count() }} {{ __('subcategories') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($category->parent)
                                                            <span class="badge badge-secondary">{{ $category->parent->name }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $category->language }}</td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $category->order ?? 0 }}</span>
                                                    </td>
                                                    <td>
                                                        @if ($category->show_at_nav == 1)
                                                            <span class="badge badge-primary">{{ __('admin.Yes') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('admin.No') }}</span>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if ($category->status == 1)
                                                            <span class="badge badge-success">{{ __('admin.Yes') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('admin.No') }}</span>
                                                        @endif

                                                    </td>


                                                    <td>
                                                        <a href="{{ route('admin.category.edit', $category->id) }}" class="btn btn-primary"><i
                                                                class="fas fa-edit"></i></a>
                                                        <a href="{{ route('admin.category.destroy', $category->id) }}" class="btn btn-danger delete-item"><i
                                                                class="fas fa-trash-alt"></i></a>
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
                "columnDefs": [{
                    "sortable": false,
                    "targets": [2, 3]
                }]
            });
        @endforeach
    </script>
@endpush
