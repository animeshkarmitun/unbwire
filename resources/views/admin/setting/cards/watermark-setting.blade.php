<div class="card border border-primary">
    <div class="card-body">
        <form action="{{ route('admin.watermark-setting.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <div class="control-label">{{ __('admin.Enable Watermark') }}</div>
                <label class="custom-switch mt-2">
                    <input type="checkbox" name="enabled" class="custom-switch-input" value="1" {{ $watermarkSetting->enabled ?? false ? 'checked' : '' }}>
                    <span class="custom-switch-indicator"></span>
                </label>
            </div>

            <div class="form-group">
                <label>{{ __('admin.Watermark Image') }}</label>
                <div id="watermark-preview" class="image-preview" style="position: relative; min-height: 150px; border: 1px solid #ddd; border-radius: 4px; padding: 10px; margin-bottom: 10px;">
                    @if(isset($watermarkSetting) && $watermarkSetting->watermark_image)
                        <img src="{{ asset($watermarkSetting->watermark_image) }}" alt="Watermark" style="max-width: 200px; max-height: 150px;">
                    @else
                        <p class="text-muted">{{ __('admin.No watermark image uploaded') }}</p>
                    @endif
                </div>
                <input type="file" name="watermark_image" class="form-control" accept="image/*">
                <small class="form-text text-muted">{{ __('admin.Upload a PNG image with transparent background for best results') }}</small>
                @error('watermark_image')
                <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('admin.Watermark Size') }} (%)</label>
                        <input type="number" name="watermark_size" class="form-control" value="{{ $watermarkSetting->watermark_size ?? 20 }}" min="1" max="100">
                        <small class="form-text text-muted">{{ __('admin.Size as percentage of image (1-100)') }}</small>
                        @error('watermark_size')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('admin.Opacity') }} (%)</label>
                        <input type="number" name="opacity" class="form-control" value="{{ $watermarkSetting->opacity ?? 100 }}" min="1" max="100">
                        <small class="form-text text-muted">{{ __('admin.Watermark transparency (1-100)') }}</small>
                        @error('opacity')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('admin.Offset') }} (px)</label>
                        <input type="number" name="offset" class="form-control" value="{{ $watermarkSetting->offset ?? 10 }}" min="0">
                        <small class="form-text text-muted">{{ __('admin.Distance from edges in pixels') }}</small>
                        @error('offset')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('admin.Position') }}</label>
                <select name="position" class="form-control">
                    <option value="center" {{ (isset($watermarkSetting) && $watermarkSetting->position == 'center') || !isset($watermarkSetting) ? 'selected' : '' }}>{{ __('admin.Center') }}</option>
                    <option value="top-left" {{ isset($watermarkSetting) && $watermarkSetting->position == 'top-left' ? 'selected' : '' }}>{{ __('admin.Top Left') }}</option>
                    <option value="top-center" {{ isset($watermarkSetting) && $watermarkSetting->position == 'top-center' ? 'selected' : '' }}>{{ __('admin.Top Center') }}</option>
                    <option value="top-right" {{ isset($watermarkSetting) && $watermarkSetting->position == 'top-right' ? 'selected' : '' }}>{{ __('admin.Top Right') }}</option>
                    <option value="middle-left" {{ isset($watermarkSetting) && $watermarkSetting->position == 'middle-left' ? 'selected' : '' }}>{{ __('admin.Middle Left') }}</option>
                    <option value="middle-right" {{ isset($watermarkSetting) && $watermarkSetting->position == 'middle-right' ? 'selected' : '' }}>{{ __('admin.Middle Right') }}</option>
                    <option value="bottom-left" {{ isset($watermarkSetting) && $watermarkSetting->position == 'bottom-left' ? 'selected' : '' }}>{{ __('admin.Bottom Left') }}</option>
                    <option value="bottom-center" {{ isset($watermarkSetting) && $watermarkSetting->position == 'bottom-center' ? 'selected' : '' }}>{{ __('admin.Bottom Center') }}</option>
                    <option value="bottom-right" {{ isset($watermarkSetting) && $watermarkSetting->position == 'bottom-right' ? 'selected' : '' }}>{{ __('admin.Bottom Right') }}</option>
                </select>
                @error('position')
                <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('admin.Save') }}</button>
        </form>
    </div>
</div>

