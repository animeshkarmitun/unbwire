@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Upload AP Photo</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>Upload Photos</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ap-photo.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" id="category" class="form-control select2">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subcategory (Optional)</label>
                                <select name="sub_category_id" id="sub_category" class="form-control select2">
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tags</label>
                        <select name="tags[]" class="form-control select2" multiple>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Upload Photos (Multiple)</label>
                        <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">You can select multiple images. Max 5MB per image.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Fetch subcategories based on category
            $('#category').on('change', function() {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        method: 'GET',
                        url: "{{ route('admin.ap-photo.fetch-subcategories') }}",
                        data: { category_id: categoryId },
                        success: function(data) {
                            $('#sub_category').html('<option value="">Select Subcategory</option>');
                            $.each(data, function(index, item) {
                                $('#sub_category').append(`<option value="${item.id}">${item.name}</option>`);
                            });
                        }
                    });
                } else {
                    $('#sub_category').html('<option value="">Select Subcategory</option>');
                }
            });
        });
    </script>
@endpush
