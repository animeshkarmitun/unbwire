@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1>{{ __('admin.News') }}</h1>
            <div>
                <a href="{{ route('admin.news.index', ['lang' => $news->language]) }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ $news->title }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        @if (!empty($news->subtitle))
                            <h6 class="text-muted mb-3">{{ $news->subtitle }}</h6>
                        @endif

                        @if (!empty($news->image))
                            <img src="{{ asset($news->image) }}" alt="{{ $news->title }}" class="img-fluid rounded mb-3"
                                style="max-height: 350px;">
                        @endif
                    </div>
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $news->id }}</td>
                                </tr>
                                <tr>
                                    <th>Language</th>
                                    <td>{{ strtoupper($news->language) }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>{{ $news->category->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Author</th>
                                    <td>{{ $news->author->name ?? ($news->auther->name ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge {{ $news->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $news->status ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Approved</th>
                                    <td>
                                        <span class="badge {{ $news->is_approved ? 'badge-success' : 'badge-warning' }}">
                                            {{ $news->is_approved ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ optional($news->created_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ optional($news->updated_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($news->tags->count())
                    <div class="mb-3">
                        <strong>Tags:</strong>
                        @foreach ($news->tags as $tag)
                            <span class="badge badge-info mr-1">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mb-3">
                    <h5>Content</h5>
                    <div class="border rounded p-3">
                        {!! $news->content !!}
                    </div>
                </div>

                @if (!empty($news->meta_title) || !empty($news->meta_description))
                    <div class="mt-4">
                        <h5>SEO</h5>
                        <div class="border rounded p-3">
                            <p class="mb-2"><strong>Meta Title:</strong> {{ $news->meta_title ?? '-' }}</p>
                            <p class="mb-0"><strong>Meta Description:</strong> {{ $news->meta_description ?? '-' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

