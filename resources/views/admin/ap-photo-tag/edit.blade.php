@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Edit AP Photo Tag</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>Edit Tag</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ap-photo-tag.update', $tag->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $tag->name) }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </section>
@endsection
