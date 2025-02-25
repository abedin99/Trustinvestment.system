<x-app-layout>

    <!-- Meta Title -->
    <x-slot name="meta_title">
        Change Password
    </x-slot>

    <x-slot name="css">

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

    <x-slot name="header_components">

    </x-slot>

    <x-slot name="footer_components">

    </x-slot>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Change Password</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Password</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#settings"
                                        data-toggle="tab">Password</a></li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="settings">
                                    <form class="form-horizontal" action="{{ route('password.update') }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <!-- Validation Errors -->
                                        <x-validation-errors class="mb-4 text-danger" :errors="$errors" />
                                        <div class="form-group row">
                                            <label for="current_password" class="col-sm-2 col-form-label">
                                                {{ __('Current Password') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="current_password"
                                                        value="{{ old('current_password') }}" id="current_password"
                                                        autocomplete="current-password"
                                                        placeholder="Enter current password">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary togglePassword"
                                                            type="button" data-target="current_password">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>

                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span class="fas fa-lock"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <x-input-error :messages="$errors->updatePassword->get('current_password')"
                                                    class="mt-2 text-danger list-unstyled" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="password" class="col-sm-2 col-form-label">
                                                {{ __('New Password') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password"
                                                        value="{{ old('password') }}" id="password"
                                                        autocomplete="new-password" placeholder="Enter new password">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary togglePassword"
                                                            type="button" data-target="password">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>

                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span class="fas fa-lock"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <x-input-error :messages="$errors->updatePassword->get('password')"
                                                    class="mt-2 text-danger list-unstyled" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="password_confirmation" class="col-sm-2 col-form-label">
                                                {{ __('New Password') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="password" class="form-control"
                                                        name="password_confirmation"
                                                        value="{{ old('password_confirmation') }}"
                                                        id="password_confirmation" autocomplete="new-password"
                                                        placeholder="Password confirmation">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary togglePassword"
                                                            type="button" data-target="password_confirmation">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>

                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span class="fas fa-lock"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')"
                                                    class="mt-2 text-danger list-unstyled" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-danger">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
</x-app-layout>
