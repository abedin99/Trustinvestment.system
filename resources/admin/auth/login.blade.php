<x-guest-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        Admin Sign In
    </x-slot>

    <x-slot name="css">

    </x-slot>
    
    <x-slot name="js">
        <script>
            $(document).ready(function() {
                $('#togglePassword').on('click', function() {
                    var passwordField = $('#password');
                    var icon = $(this).find('i');

                    // Toggle password visibility
                    if (passwordField.attr('type') === 'password') {
                        passwordField.attr('type', 'text');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        passwordField.attr('type', 'password');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                });
            });
        </script>
    </x-slot>

    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-success">
            <div class="card-header text-center">
                <a href="{{ route('login') }}" class="h3">
                    <img src="{{ asset('images/trustway-logo.png') }}" alt="{{ Setting::get('app_name') }}"
                        style="height: 100px;">
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg h3">Admin Login</p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

                <!-- Validation Errors -->
                <x-validation-errors class="mb-4 text-danger" :errors="$errors" />

                <form action="{{ route('admin.login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                            placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember_me" name="remember">
                                <label for="remember_me">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-success btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                {{-- <div class="social-auth-links text-center mt-2 mb-3">
                    <a href="#" class="btn btn-block btn-primary">
                        <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
                    </a>
                    <a href="#" class="btn btn-block btn-danger">
                        <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                    </a>
                </div> --}}
                <!-- /.social-auth-links -->

                @if (Route::has('password.request'))
                    <p class="mb-1">
                        <a href="{{ route('admin.password.request') }}">I forgot my password</a>
                    </p>
                @endif
                @if (Route::has('admin.register'))
                    <p class="mb-0">
                        <a href="{{ route('admin.register') }}" class="text-center">Register a new membership</a>
                    </p>
                @endif
                @if (Route::has('login'))
                    <p class="mb-0">
                        <a href="{{ route('login') }}" class="text-center">Login As User</a>
                    </p>
                @endif
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
</x-guest-layout>
