@extends('frontend.layouts.master')

@section('title', 'Checkout - ' . $package->name)

@section('content')
<section class="pb-80">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Breadcrumb -->
                <ul class="breadcrumbs bg-light mb-4">
                    <li class="breadcrumbs__item">
                        <a href="{{ url('/') }}" class="breadcrumbs__url">
                            <i class="fa fa-home"></i> {{ __('frontend.Home') }}</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="{{ route('subscription.plans') }}" class="breadcrumbs__url">Subscription Plans</a>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="javascript:;" class="breadcrumbs__url">Checkout</a>
                    </li>
                </ul>
                <!-- End breadcrumb -->

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Complete Your Subscription</h4>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-3">{{ $package->name }}</h5>
                                <p class="text-muted">{{ $package->description }}</p>

                                <form action="{{ route('subscription.subscribe', $package->id) }}" method="POST">
                                    @csrf

                                    <div class="form-group">
                                        <label for="billing_period">Billing Period <span class="text-danger">*</span></label>
                                        <select name="billing_period" id="billing_period" class="form-control" required>
                                            <option value="monthly" {{ old('billing_period', 'monthly') == 'monthly' ? 'selected' : '' }}>
                                                Monthly - {{ $package->currency }} {{ number_format($package->price, 2) }}
                                            </option>
                                            <option value="yearly" {{ old('billing_period') == 'yearly' ? 'selected' : '' }}>
                                                Yearly - {{ $package->currency }} {{ number_format($package->price * 12, 2) }} (Save {{ number_format($package->price * 12 * 0.1, 2) }})
                                            </option>
                                        </select>
                                        @error('billing_period')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                        <select name="payment_method" id="payment_method" class="form-control" required>
                                            <option value="manual" {{ old('payment_method', 'manual') == 'manual' ? 'selected' : '' }}>Manual Payment (Admin Approval)</option>
                                            <option value="stripe" disabled>Stripe (Coming Soon)</option>
                                            <option value="paypal" disabled>PayPal (Coming Soon)</option>
                                        </select>
                                        @error('payment_method')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if(old('payment_method') == 'manual' || old('payment_method', 'manual') == 'manual')
                                        <div class="form-group" id="transaction_id_group">
                                            <label for="payment_transaction_id">Transaction ID (Optional)</label>
                                            <input type="text" name="payment_transaction_id" id="payment_transaction_id" 
                                                   class="form-control" value="{{ old('payment_transaction_id') }}" 
                                                   placeholder="Enter transaction ID if available">
                                            @error('payment_transaction_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="auto_renew" value="1" 
                                                   class="custom-control-input" id="auto_renew" checked>
                                            <label class="custom-control-label" for="auto_renew">
                                                Auto-renew subscription
                                            </label>
                                        </div>
                                    </div>

                                    @if($activeSubscription)
                                        <div class="alert alert-warning">
                                            <strong>Note:</strong> You currently have an active subscription. 
                                            Subscribing to this plan will replace your current subscription.
                                        </div>
                                    @endif

                                    <button type="submit" class="btn btn-primary btn-lg">
                                        Complete Subscription
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Plan:</span>
                                    <strong>{{ $package->name }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Billing:</span>
                                    <strong id="billing-display">Monthly</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span><strong>Total:</strong></span>
                                    <strong id="total-display">{{ $package->currency }} {{ number_format($package->price, 2) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <h6>What's Included:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> News Articles</li>
                                    @if($package->access_images)
                                        <li><i class="fas fa-check text-success"></i> Images</li>
                                    @endif
                                    @if($package->access_videos)
                                        <li><i class="fas fa-check text-success"></i> Videos</li>
                                    @endif
                                    @if($package->access_exclusive)
                                        <li><i class="fas fa-check text-success"></i> Exclusive Content</li>
                                    @endif
                                    @if($package->ad_free)
                                        <li><i class="fas fa-check text-success"></i> Ad-Free</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('content')
<script>
    $(document).ready(function() {
        $('#billing_period').on('change', function() {
            const period = $(this).val();
            const monthlyPrice = {{ $package->price }};
            const yearlyPrice = monthlyPrice * 12;
            
            if (period === 'yearly') {
                $('#billing-display').text('Yearly');
                $('#total-display').text('{{ $package->currency }} ' + yearlyPrice.toFixed(2));
            } else {
                $('#billing-display').text('Monthly');
                $('#total-display').text('{{ $package->currency }} ' + monthlyPrice.toFixed(2));
            }
        });
    });
</script>
@endpush
@endsection

