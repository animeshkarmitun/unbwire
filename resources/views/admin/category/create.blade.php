@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Category') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.Create Category') }}</h4>

            </div>
            <div class="card-body">
                <form action="{{ route('admin.category.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('admin.Language') }}</label>
                        <select name="language" id="language-select" class="form-control select2">
                            <option value="">--{{ __('admin.Select') }}--</option>
                            @foreach ($languages as $lang)
                                <option value="{{ $lang->lang }}">{{ $lang->name }}</option>
                            @endforeach
                        </select>
                        @error('language')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                    </div>
                    <div class="form-group">
                        <label for="">{{ __('admin.Name') }}</label>
                        <input name="name" type="text" class="form-control" id="name">
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Parent Category') }} ({{ __('Optional') }})</label>
                        <select name="parent_id" id="parent_id" class="form-control select2">
                            <option value="">{{ __('None (Main Category)') }}</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" data-language="{{ $parent->language }}">
                                    {{ $parent->name }} ({{ $parent->language }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('Select a parent category to make this a subcategory. Leave empty for main category.') }}</small>
                        @error('parent_id')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('admin.Show at Nav') }} </label>
                        <select name="show_at_nav" id="" class="form-control">
                            <option value="0">{{ __('admin.No') }}</option>
                            <option value="1">{{ __('admin.Yes') }}</option>
                        </select>
                        @error('defalut')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('admin.Status') }}</label>
                        <select name="status" id="" class="form-control">
                            <option value="1">{{ __('admin.Active') }}</option>
                            <option value="0">{{ __('admin.Inactive') }}</option>
                        </select>
                        @error('status')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Menu Order</label>
                        <input name="order" type="number" class="form-control" id="order" value="0" min="0" placeholder="0">
                        <small class="form-text text-muted">Lower numbers appear first in menu. Default: 0</small>
                        @error('order')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('admin.Create') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Filter parent categories based on selected language
            $('#language-select').on('change', function() {
                const selectedLanguage = $(this).val();
                const $parentSelect = $('#parent_id');
                
                if (selectedLanguage) {
                    // Show only categories matching selected language
                    $parentSelect.find('option[data-language]').each(function() {
                        if ($(this).data('language') === selectedLanguage) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                    
                    // Reset selection if current selection doesn't match
                    if ($parentSelect.val() && $parentSelect.find('option:selected').data('language') !== selectedLanguage) {
                        $parentSelect.val('').trigger('change');
                    }
                } else {
                    // Show all if no language selected
                    $parentSelect.find('option[data-language]').show();
                }
            });
        });
    </script>
@endpush
