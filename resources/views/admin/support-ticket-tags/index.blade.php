@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Support Ticket Tags') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('All Tags') }}</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.support-ticket-tags.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Create New Tag') }}
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="tagsTable">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Color') }}</th>
                                <th>{{ __('Tickets Count') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tags as $tag)
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: {{ $tag->color ?? '#6c757d' }}; padding: 8px 12px;">
                                            {{ $tag->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 30px; height: 30px; background-color: {{ $tag->color ?? '#6c757d' }}; border: 1px solid #ddd; border-radius: 4px;"></div>
                                            <code>{{ $tag->color ?? '#6c757d' }}</code>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $tag->tickets_count }}</span>
                                    </td>
                                    <td>{{ $tag->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.support-ticket-tags.edit', $tag->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.support-ticket-tags.destroy', $tag->id) }}" class="btn btn-danger btn-sm delete-item">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('No tags found') }}</td>
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
        $(document).ready(function() {
            $("#tagsTable").dataTable({
                "columnDefs": [{
                    "sortable": false,
                    "targets": [4]
                }],
                "order": [[0, "asc"]]
            });
        });
    </script>
@endpush






























