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
                                <label>{{ __('admin.Name') }} <span class="text-danger">*</span></label>
                                <input name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                                @error('name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('admin.Currency') }} <span class="text-danger">*</span></label>
                                <select name="currency" class="form-control" required>
                                    <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="BDT" {{ old('currency') == 'BDT' ? 'selected' : '' }}>BDT</option>
                                </select>
                                @error('currency')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('admin.Price') }} <span class="text-danger">*</span></label>
                                <input name="price" type="number" step="0.01" min="0" class="form-control" value="{{ old('price') }}" required>
                                @error('price')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('admin.Billing Period') }} <span class="text-danger">*</span></label>
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
                        <label>{{ __('admin.Description') }}</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Max Articles Per Day') }}</label>
                        <input name="max_articles_per_day" type="number" min="1" class="form-control" 
                               value="{{ old('max_articles_per_day') }}" 
                               placeholder="Leave empty for unlimited">
                        @error('max_articles_per_day')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>{{ __('admin.Sort Order') }}</label>
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
                                    <label class="custom-control-label" for="access_news">{{ __('admin.Access News') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_images" value="1" class="custom-control-input" id="access_images">
                                    <label class="custom-control-label" for="access_images">{{ __('admin.Access Images') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_videos" value="1" class="custom-control-input" id="access_videos">
                                    <label class="custom-control-label" for="access_videos">{{ __('admin.Access Videos') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="access_exclusive" value="1" class="custom-control-input" id="access_exclusive">
                                    <label class="custom-control-label" for="access_exclusive">{{ __('admin.Access Exclusive') }}</label>
                                </div>
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
                                    <label class="custom-control-label" for="ad_free">{{ __('admin.Ad Free') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="priority_support" value="1" class="custom-control-input" id="priority_support">
                                    <label class="custom-control-label" for="priority_support">{{ __('admin.Priority Support') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active" checked>
                                    <label class="custom-control-label" for="is_active">{{ __('admin.Active') }}</label>
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

