@extends('frontend.layouts.app')

@section('meta_title', 'Login')

@section('script')
@endsection

@section('content')

    <!-- Login Form -->
    <div class="py-5 bg-primary">
        <div class="container my-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7 animated fadeInLeft">
                    <div class="text-sm-center text-md-start">
                        <h4 class="text-white text-uppercase fw-bold mb-4">Welcome To {{ Setting::get('app_name') }}
                        </h4>
                        <h1 class="display-1 text-white mb-4">Life Insurance Makes You Happy</h1>
                        <p class="mb-5 fs-5 text-secondary">Lorem Ipsum is simply dummy text of the printing and typesetting
                            industry. Lorem Ipsum has been the industry's standard dummy...
                        </p>
                        <div class="d-flex justify-content-center justify-content-md-start flex-shrink-0 mb-4">
                            <a class="btn btn-dark rounded-pill py-3 px-4 px-md-5 ms-2"
                                href="{{ route('about.index') }}">Learn
                                More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 animated fadeInRight">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h3 class="text-light">Login Form</h3>
                        </div>
                        <div class="card-body">

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
        
                                <div class="form-group mb-3">
                                    <label class="text-normal text-dark mb-2">Email / Username <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg"
                                        placeholder="Enter Your Email Or Username" required autofocus autocomplete="username">
                                    @error('email')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
        
                                <div class="form-group mb-3">
                                    <label class="text-normal text-dark mb-2">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Password"required
                                        autocomplete="current-password">
                                    @error('password')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
        
                                <div class="form-group">
                                    <div class="peers ai-c jc-sb fxw-nw">
                                        <div class="peer">
                                            <div class="checkbox checkbox-circle checkbox-info peers ai-c">
                                                <input type="checkbox" id="remember_me" name="remember" class="peer">
                                                <label for="remember_me" class="peers peer-greed js-sb ai-c"><span
                                                        class="peer peer-greed">{{ __('Remember me') }}</span></label>
        
                                                @error('remember')
                                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="my-3" style="text-align: right;">
                                            <button type="submit" class="btn btn-primary px-5 ">Login</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
        
                            @if (Route::has('password.request'))
                                <a class="text-primary d-block mt-2" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                            @if (Route::has('register'))
                                <a class="text-primary d-block mt-2" href="{{ route('register') }}">
                                    {{ __('Create a new account') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Form -->

@endsection
