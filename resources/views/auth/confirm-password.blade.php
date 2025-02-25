<x-guest-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        Confirm Password
    </x-slot>

    <x-slot name="css">
    </x-slot>


    <div class="register-box">
        <!-- /.register-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ route('login') }}" class="h3">{{ Setting::get('app_name') }}</a>
            </div>
            <div class="card-body">
                <p class="register-box-msg">Reset password to start your session</p>

                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

                <!-- Validation Errors -->
                <x-validation-errors class="mb-4 text-danger" :errors="$errors" />


                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password" class="form-control w-100" type="password" name="password" required
                            autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="d-block">
                        <x-primary-button class="bg-success">
                            {{ __('Confirm') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.register-box -->
</x-guest-layout>
