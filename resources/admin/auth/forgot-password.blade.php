<x-guest-layout>

    <!-- Meta Title -->
    <x-slot name="meta_title">
        Forgot your password?
    </x-slot>

    <x-slot name="css">
    </x-slot>

    <div class="register-box">
        <!-- /.register-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ route('admin.login') }}" class="h3">{{ Setting::get('app_name') }}</a>
            </div>
            <div class="card-body">
                <p class="register-box-msg">Reset password to start your session</p>

                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

                <!-- Validation Errors -->
                <x-validation-errors class="mb-4 text-danger" :errors="$errors" />

                <form method="POST" action="{{ route('admin.password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="form-control" type="email" name="email"
                            :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-primary-button class="bg-info d-block w-100">
                            {{ __('Email Password Reset Link') }}
                        </x-primary-button>
                    </div>
                </form>


                <a class="text-primary d-block mt-3" href="{{ route('admin.login') }}">
                    {{ __('Back to login page') }}
                </a>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.register-box -->
</x-guest-layout>
