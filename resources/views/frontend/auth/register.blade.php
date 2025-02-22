@extends('frontend.layouts.app')

@section('meta_title', 'Register')

@section('script')
@endsection

@section('content')

    <!-- Login Form -->
    <div class="py-5 bg-info">
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
                            <h3 class="text-light">Registration Form</h3>
                        </div>
                        <div class="card-body">

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="form-group mb-3">
                                    <label class="text-normal text-dark mb-2">Referral ID <i
                                            class="text-danger text-small">(It is not mandatory to give.)</i></label>
                                    <input type="text" name="referral_id" value="{{ old('referral_id') }}"
                                        class="form-control form-control-lg" placeholder="Enter your referral username">
                                    @error('referral_id')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="text-normal text-dark mb-2">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="form-control form-control-lg" placeholder="Enter your email address"
                                        required>
                                    @error('email')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="text-normal text-dark mb-2">Full name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="form-control form-control-lg" placeholder="Enter your fullname" required>
                                    @error('name')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="text-normal text-dark mb-2">Username <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="username" value="{{ old('username') }}"
                                        class="form-control form-control-lg" placeholder="Enter your username" required
                                        autocomplete="Username">
                                    @error('username')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="text-normal text-dark mb-2">Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control form-control-lg"
                                        placeholder="Enter Password"required autocomplete="current-password">
                                    @error('password')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="text-normal text-dark mb-2">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control form-control-lg"
                                        placeholder="Enter Confirm Password"required autocomplete="new-password">
                                    @error('password_confirmation')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="peers ai-c jc-sb fxw-nw">
                                        <div class="peer">
                                            <div class="checkbox checkbox-circle checkbox-info peers ai-c">
                                                <input type="checkbox" id="acknowledgment" name="acknowledgment"
                                                    class="peer">
                                                <label for="acknowledgment" class="peers peer-greed js-sb ai-c"><span
                                                        class="peer peer-greed">{{ __('I accept terms and conditions') }}</span></label>

                                                @error('acknowledgment')
                                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="my-3" style="text-align: right;">
                                            <button type="submit" class="btn btn-success px-5 ">Register Now</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if (Route::has('register'))
                                <a class="text-primary d-block mt-2" href="{{ route('register') }}">
                                    {{ __('Already registered?') }}
                                </a>
                            @endif
                            @if (Route::has('password.request'))
                                <a class="text-primary d-block mt-2" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
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
