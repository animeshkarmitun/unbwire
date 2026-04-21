@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Create AP Photo Category</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>Create New Category</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ap-photo-category.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    </div>

                    <div class="form-group">
                        <label>Parent Category</label>
                        <select name="parent_id" class="form-control select2">
                            <option value="">None (Top Level)</option>
                            @foreach ($parentCategories as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Subcategories cannot have their own subcategories.</small>
                    </div>

                    <div class="form-group">
                        <label>Order</label>
                        <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </section>
@endsection
