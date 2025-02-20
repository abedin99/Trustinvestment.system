<x-guest-layout>
    <x-slot name="meta_title">
        Login
    </x-slot>

    {{-- Session Status  --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="d-n@sm- peer peer-greed h-100 pos-r bgr-n bgpX-c bgpY-c bgsz-cv"
        style="background-image:url({{ asset('assets/static/images/bg.jpg') }})">
        <div class="pos-a centerXY">
            <div class="bgc-white bdrs-50p pos-r" style="width:120px;height:120px"><img class="pos-a centerXY"
                    src="{{ asset('assets/static/images/logo.png') }}" alt=""></div>
        </div>
    </div>
    <div class="col-12 col-md-4 peer pX-40 pY-80 h-100 bgc-white scrollable pos-r" style="min-width:320px">
        <h4 class="fw-300 c-grey-900 mB-40">Login</h4>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="text-normal text-dark">Email / Username <span class="text-danger">*</span></label>
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
</x-guest-layout>
