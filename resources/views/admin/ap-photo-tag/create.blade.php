@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Create AP Photo Tag</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>Create Tag</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ap-photo-tag.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </section>
@endsection
