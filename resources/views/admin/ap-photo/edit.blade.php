@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Edit AP Photo</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>Edit Photo Entry</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ap-photo.update', $photo->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" id="category" class="form-control select2">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $photo->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subcategory (Optional)</label>
                                <select name="sub_category_id" id="sub_category" class="form-control select2">
                                    <option value="">Select Subcategory</option>
                                    @foreach ($subCategories as $sub)
                                        <option value="{{ $sub->id }}" {{ $photo->sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tags</label>
                        <select name="tags[]" class="form-control select2" multiple>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, $photo->tags->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $photo->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Existing Photos</label>
                        <div class="row">
                            @foreach ($photo->items as $item)
                                <div class="col-md-2 mb-3 text-center" id="item-{{ $item->id }}">
                                    <img src="{{ $item->file_url }}" width="100%" class="img-thumbnail" alt="">
                                    <button type="button" class="btn btn-danger btn-sm mt-1 delete-photo-item" data-id="{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Add More Photos (Multiple)</label>
                        <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">You can select multiple images. Max 5MB per image.</small>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $photo->status ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$photo->status ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
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

            // Delete specific photo item
            $('.delete-photo-item').on('click', function() {
                let id = $(this).data('id');
                if (confirm('Are you sure you want to delete this photo?')) {
                    $.ajax({
                        method: 'DELETE',
                        url: "{{ url('admin/ap-photo/delete-item') }}/" + id,
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(data) {
                            if (data.status === 'success') {
                                $('#item-' + id).remove();
                                Swal.fire('Deleted!', data.message, 'success');
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
