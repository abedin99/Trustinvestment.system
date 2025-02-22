<x-admin-guest-layout>
    {{-- meta_title --}}
    <x-slot name="meta_title">
        Forgot your password?
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

                    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </div>

                    <form method="POST" action="{{ route('admin.password.email') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" required autofocus placeholder="Enter email">
                            @if ($errors->get('email'))
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            @else
                                <small id="emailHelp" class="form-text text-muted">We'll never share your email with
                                    anyone
                                    else.</small>
                            @endif
                        </div>

                        <div class="my-4 text-right">
                            <x-primary-button>
                                {{ __('Email Password Reset Link') }}
                            </x-primary-button>
                        </div>
                    </form>
                    @if (Route::has('admin.login'))
                        <a class="text-primary d-block mt-2" href="{{ route('admin.login') }}">
                            {{ __('Already have an account.') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-guest-layout>
