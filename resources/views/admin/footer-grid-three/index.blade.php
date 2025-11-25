@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Footer Grid Three') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.All Footer Grid Three Links') }}</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.footer-grid-three.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('admin.Create new') }}
                    </a>
                </div>
            </div>

            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    @foreach ($languages as $language)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->index === 0 ? 'active' : '' }}" id="home-tab2" data-toggle="tab"
                                href="#home-{{ $language->lang }}" role="tab" aria-controls="home"
                                aria-selected="true">{{ $language->name }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content tab-bordered" id="myTab3Content">
                    @foreach ($languages as $language)
                        @php
                            $footerLinks = \App\Models\FooterGridThree::where('language', $language->lang)->orderByDesc('id')->get();
                            $footerTitle = \App\Models\FooterTitle::where(['key' => 'grid_three_title', 'language' => $language->lang])->first();
                        @endphp
                        <div class="tab-pane fade show {{ $loop->index === 0 ? 'active' : '' }}"
                            id="home-{{ $language->lang }}" role="tabpanel" aria-labelledby="home-tab2">
                            <div class="card-body">
                                <form action="{{ route('admin.footer-grid-three-title') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="form-group">
                                        <label for="">{{ __('admin.Grid Title') }}</label>
                                        <div class="input-group">
                                            <input type="text" name="title" class="form-control" value="{{ @$footerTitle->value }}">
                                            <input type="hidden" name="language" value="{{ $language->lang }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">{{ __('admin.Save Title') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-{{ $language->lang }}">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>{{ __('admin.Name') }}</th>
                                                <th>{{ __('admin.Url') }}</th>
                                                <th>{{ __('admin.Language') }}</th>
                                                <th>{{ __('admin.Status') }}</th>
                                                <th>{{ __('admin.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($footerLinks as $link)
                                                <tr>
                                                    <td>{{ $link->id }}</td>
                                                    <td>{{ $link->name }}</td>
                                                    <td>{{ $link->url }}</td>
                                                    <td>{{ $link->language }}</td>
                                                    <td>
                                                        @if ($link->status == 1)
                                                            <span class="badge badge-success">{{ __('admin.Active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('admin.Inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.footer-grid-three.edit', $link->id) }}" class="btn btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('admin.footer-grid-three.destroy', $link->id) }}" class="btn btn-danger delete-item">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
                    "targets": [4]
                }]
            });
        @endforeach
    </script>
@endpush


