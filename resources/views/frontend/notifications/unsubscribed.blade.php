@extends('frontend.layouts.master')

@section('title', 'Unsubscribed')

@section('content')
<section class="pb-80">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap__about-us text-center">
                    <div class="alert alert-success">
                        <h3><i class="fas fa-check-circle"></i> Successfully Unsubscribed</h3>
                        <p class="mb-0">You have been unsubscribed from email notifications.</p>
                        <p class="mt-2">Email: <strong>{{ $subscriber->email }}</strong></p>
                    </div>
                    <p>You will no longer receive email notifications about new articles.</p>
                    <p>If you change your mind, you can resubscribe from your profile settings.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary mt-3">Go to Home</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
