@extends('frontend.layouts.app')

@section('meta_title', 'Login')

@section('script')
@endsection

@section('content')

    <!-- Login Form -->
    <div class="py-5 bg-primary">
        <div class="container my-5">
            <div class="row g-4 align-items-center justify-content-center">
                <div class="col-lg-6 animated fadeInTop">
                    <div class="text-sm-center text-center">
                        <h4 class="text-white text-uppercase fw-bold mb-4">
                            Thanks for signing up!
                        </h4>
                        <p class="mb-5 fs-5 text-secondary">
                            Before getting started, could you verify your email address by clicking on the link we just
                            emailed to you? If you didn't receive the email, we will gladly send you another.
                        </p>
                    </div>

                    <div class="card">
                        <div class="card-body">

                            @if (session('status') == 'verification-link-sent')
                                <div class="alert alert-success" role="alert">
                                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('verification.send') }}" class="text-center">
                                @csrf

                                <div>
                                    <x-primary-button class="btn-lg">
                                        {{ __('Resend Verification Email') }}
                                    </x-primary-button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('logout') }}" class="text-center pt-5">
                                @csrf
                    
                                <button type="submit" class="btn btn-lg btn-danger px-4">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Form -->

@endsection
