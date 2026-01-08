@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Create Subscription Package') }}</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>{{ __('admin.Create New Package') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subscription-package.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                                @error('name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Currency <span class="text-danger">*</span></label>
                                <input name="currency" type="text" class="form-control" value="{{ old('currency', 'USD') }}" maxlength="3" required>
                                @error('currency')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Price <span class="text-danger">*</span></label>
                                <input name="price" type="number" step="0.01" min="0" class="form-control" value="{{ old('price') }}" required>
                                @error('price')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Billing Period <span class="text-danger">*</span></label>
                                <select name="billing_period" class="form-control" required>
                                    <option value="monthly" {{ old('billing_period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ old('billing_period') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                @error('billing_period')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Max Articles Per Day</label>
                        <input name="max_articles_per_day" type="number" min="1" class="form-control" 
                               value="{{ old('max_articles_per_day') }}" 
                               placeholder="Leave empty for unlimited">
                        @error('max_articles_per_day')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Sort Order</label>
                        <input name="sort_order" type="number" min="0" class="form-control" value="{{ old('sort_order', 0) }}">
                        @error('sort_order')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr>
                    <h5>{{ __('admin.Access Permissions') }}</h5>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_news" value="1" class="custom-control-input" id="access_news" checked>
                                    <label class="custom-control-label" for="access_news">Access News</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_images" value="1" class="custom-control-input" id="access_images">
                                    <label class="custom-control-label" for="access_images">Access Images</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_videos" value="1" class="custom-control-input" id="access_videos">
                                    <label class="custom-control-label" for="access_videos">Access Videos</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_exclusive" value="1" class="custom-control-input" id="access_exclusive">
                                    <label class="custom-control-label" for="access_exclusive">Access Exclusive</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_bangla" value="1" class="custom-control-input" id="access_bangla" {{ old('access_bangla') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="access_bangla">Access Bangla</label>
                                </div>
                                <small class="form-text text-muted">Enable to allow access to Bangla content. If disabled, Bangla content will be restricted.</small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_english" value="1" class="custom-control-input" id="access_english" {{ old('access_english') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="access_english">Access English</label>
                                </div>
                                <small class="form-text text-muted">Enable to allow access to English content. If disabled, English content will be restricted.</small>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5>{{ __('admin.Features') }}</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="ad_free" value="1" class="custom-control-input" id="ad_free">
                                    <label class="custom-control-label" for="ad_free">Ad Free</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="priority_support" value="1" class="custom-control-input" id="priority_support">
                                    <label class="custom-control-label" for="priority_support">Priority Support</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" checked>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('admin.Create') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

