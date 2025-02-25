<x-guest-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        User Sign In
    </x-slot>

    <x-slot name="css">
        <style rel="stylesheet">
            body {
                background: url('{{ asset('/images/admin-login-bg.jpg') }}');
                background-repeat: no-repeat;
                background-size: cover;
                backdrop-filter: blur(2px);
            }
        </style>
    </x-slot>

    <x-slot name="js">
        <script>
            $(document).ready(function() {
                // Reusable function for toggling password visibility
                function togglePassword(button) {
                    var targetId = $(button).data('target'); // Get the target input ID
                    var passwordField = $('#' + targetId); // Select the input field
                    var icon = $(button).find('i'); // Select the icon inside the button

                    // Toggle password visibility and update the icon
                    if (passwordField.attr('type') === 'password') {
                        passwordField.attr('type', 'text');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        passwordField.attr('type', 'password');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                }

                // Attach event listener to buttons with the class `togglePassword`
                $(document).on('click', '.togglePassword', function() {
                    togglePassword(this); // Call the reusable function
                });
            });
        </script>
    </x-slot>

    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-success">
            <div class="card-header text-center">
                {{-- <a href="{{ route('login') }}" class="h3">{{ Setting::get('app_name') }}</a> --}}
                <a href="{{ route('login') }}" class="h3">
                    <img src="{{ asset('images/trustway-logo.png') }}" alt="{{ Setting::get('app_name') }}"
                        style="height: 100px;">
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg h4">Welcome to {{ Setting::get('app_name') }}.</p>
                <p class="login-box-msg h6">Sign in to start your session as user</p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

                <!-- Validation Errors -->
                <x-validation-errors class="mb-4 text-danger" :errors="$errors" />

                <form action="{{ route('login') }}" method="post">
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
                        <input type="password" name="password" class="form-control" id="password"
                            placeholder="Password">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary togglePassword"
                                data-target="password">
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
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
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
                        <a href="{{ route('password.request') }}">I forgot my password</a>
                    </p>
                @endif
                @if (Route::has('register'))
                    <p class="mb-0">
                        <a href="{{ route('register') }}" class="text-center">Register a new membership</a>
                    </p>
                @endif
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
</x-guest-layout>
