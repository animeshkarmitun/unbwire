@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Authors</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>All Authors</h4>
                <div class="card-header-action">
                    @if (canAccess(['author create en', 'author create', 'news all-access']))
                        <a href="{{ route('admin.author.create', 'en') }}" class="btn btn-primary mr-2">
                            <i class="fas fa-plus"></i> Create English
                        </a>
                    @endif
                    @if (canAccess(['author create bn', 'author create', 'news all-access']))
                        <a href="{{ route('admin.author.create', 'bn') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Bangla
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    @php
                        // Check if user can view all languages
                        $canViewAll = canAccess(['news all-access', 'author view']);
                    @endphp
                    @if($canViewAll)
                    <li class="nav-item">
                        <a class="nav-link {{ !$selectedLang ? 'active' : '' }}" 
                           href="{{ route('admin.author.index') }}">
                            All
                        </a>
                    </li>
                    @endif
                    @foreach ($languages as $language)
                        @php
                            // Check if user can view this language's authors
                            $canViewLang = canAccess(['news all-access', 'author view', 'author view ' . $language->lang]);
                        @endphp
                        @if($canViewLang)
                        <li class="nav-item">
                            <a class="nav-link {{ ($selectedLang == $language->lang) ? 'active' : '' }}" 
                               href="{{ route('admin.author.index', ['lang' => $language->lang]) }}">
                                {{ $language->name }}
                            </a>
                        </li>
                        @endif
                    @endforeach
                </ul>
                
                <div class="table-responsive mt-3">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Language</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($authors as $author)
                                <tr>
                                    <td>{{ $author->id }}</td>
                                    <td>
                                        @if($author->photo)
                                            <img src="{{ asset($author->photo) }}" alt="{{ $author->name }}" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" 
                                                 onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/placeholder.webp') }}';">
                                        @else
                                            <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #ddd; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td><strong>{{ $author->name }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $author->language == 'en' ? 'primary' : 'success' }}">
                                            {{ $author->language == 'en' ? 'English' : 'Bangla' }}
                                        </span>
                                    </td>
                                    <td>{{ $author->designation ?? '-' }}</td>
                                    <td>
                                        @if ($author->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.author.edit', $author->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.author.destroy', $author->id) }}" class="btn btn-danger delete-item">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No authors found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("#table").dataTable({
            "columnDefs": [{
                "sortable": false,
                "targets": [6]
            }]
        });
    </script>
@endpush

