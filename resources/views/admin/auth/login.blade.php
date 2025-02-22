<x-admin-guest-layout>
    {{-- meta_title --}}
    <x-slot name="meta_title">
        Admin Login
    </x-slot>

    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-6 pX-40 pY-80 h-100 bgc-white">
            <div class="card">
                <div class="card-header">

                    <h4 class="fw-300 c-grey-900 text-success">Admin Login</h4>
                    <h1 class="text-primary">{{ Setting::get('app_name') }}</h1>
                </div>

                <div class="card-body">

                    {{-- Session Status --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <div class="form-group">
                            <label class="text-normal text-dark">Email / Username <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                placeholder="Enter Your Email Or Username" required autofocus autocomplete="username">
                            @error('email')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="text-normal text-dark">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Password"required
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
                                <div class="peer">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (Route::has('password.request'))
                        <a class="text-primary d-block mt-2" href="{{ route('admin.password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-guest-layout>
