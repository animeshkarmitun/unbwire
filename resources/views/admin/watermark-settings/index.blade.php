@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Watermark Settings') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('Configure Watermark') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.watermark-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <div class="control-label">{{ __('Enable Watermark') }}</div>
                        <label class="custom-switch mt-2">
                            <input type="checkbox" name="enabled" class="custom-switch-input" value="1" {{ $setting->enabled ? 'checked' : '' }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Watermark Image') }}</label>
                        <div id="watermark-preview" class="image-preview" style="position: relative; min-height: 150px; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                            @if($setting->watermark_image)
                                <img src="{{ asset($setting->watermark_image) }}" alt="Watermark" style="max-width: 200px; max-height: 150px;">
                            @else
                                <p class="text-muted">{{ __('No watermark image uploaded') }}</p>
                            @endif
                        </div>
                        <input type="file" name="watermark_image" class="form-control mt-2" accept="image/*">
                        <small class="form-text text-muted">{{ __('Upload a PNG image with transparent background for best results') }}</small>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('Watermark Size (%)') }}</label>
                                <input type="number" name="watermark_size" class="form-control" value="{{ $setting->watermark_size }}" min="1" max="100">
                                <small class="form-text text-muted">{{ __('Size as percentage of image (1-100)') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('Opacity (%)') }}</label>
                                <input type="number" name="opacity" class="form-control" value="{{ $setting->opacity }}" min="1" max="100">
                                <small class="form-text text-muted">{{ __('Watermark transparency (1-100)') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('Offset (px)') }}</label>
                                <input type="number" name="offset" class="form-control" value="{{ $setting->offset }}" min="0">
                                <small class="form-text text-muted">{{ __('Distance from edges in pixels') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Position') }}</label>
                        <select name="position" class="form-control">
                            <option value="center" {{ $setting->position == 'center' ? 'selected' : '' }}>{{ __('Center') }}</option>
                            <option value="top-left" {{ $setting->position == 'top-left' ? 'selected' : '' }}>{{ __('Top Left') }}</option>
                            <option value="top-center" {{ $setting->position == 'top-center' ? 'selected' : '' }}>{{ __('Top Center') }}</option>
                            <option value="top-right" {{ $setting->position == 'top-right' ? 'selected' : '' }}>{{ __('Top Right') }}</option>
                            <option value="middle-left" {{ $setting->position == 'middle-left' ? 'selected' : '' }}>{{ __('Middle Left') }}</option>
                            <option value="middle-right" {{ $setting->position == 'middle-right' ? 'selected' : '' }}>{{ __('Middle Right') }}</option>
                            <option value="bottom-left" {{ $setting->position == 'bottom-left' ? 'selected' : '' }}>{{ __('Bottom Left') }}</option>
                            <option value="bottom-center" {{ $setting->position == 'bottom-center' ? 'selected' : '' }}>{{ __('Bottom Center') }}</option>
                            <option value="bottom-right" {{ $setting->position == 'bottom-right' ? 'selected' : '' }}>{{ __('Bottom Right') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Update Settings') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

