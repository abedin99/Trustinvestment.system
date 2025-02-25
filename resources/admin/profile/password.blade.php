<x-app-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        Change Password
    </x-slot>

    <x-slot name="css">
        <!-- Add any custom CSS here -->
    </x-slot>

    <x-slot name="js">
        <script>
            $(document).ready(function() {
                // Reusable togglePassword function
                function togglePassword(button) {
                    var targetId = $(button).data('target'); // Get the target input ID
                    var passwordField = $('#' + targetId); // Find the input field
                    var icon = $(button).find('i'); // Find the icon inside the button

                    // Toggle password visibility
                    if (passwordField.attr('type') === 'password') {
                        passwordField.attr('type', 'text');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        passwordField.attr('type', 'password');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                }

                // Attach event listener to all buttons with the class `togglePassword`
                $(document).on('click', '.togglePassword', function() {
                    togglePassword(this); // Call the reusable function
                });
            });
        </script>
    </x-slot>

    <x-slot name="header_components">
        <!-- Additional header components -->
    </x-slot>

    <x-slot name="footer_components">
        <!-- Additional footer components -->
    </x-slot>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Change Password</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Password</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#settings" data-toggle="tab">Password</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="settings">
                                    <form class="form-horizontal" action="{{ route('admin.password.update') }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')

                                        <!-- Validation Errors -->
                                        <x-validation-errors class="mb-4 text-danger" :errors="$errors" />

                                        <!-- Current Password -->
                                        <div class="form-group row">
                                            <label for="current_password" class="col-sm-2 col-form-label">Current
                                                Password</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="current_password"
                                                        id="current_password" autocomplete="current-password"
                                                        placeholder="Enter current password">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary togglePassword"
                                                            type="button" data-target="current_password">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <x-input-error :messages="$errors->updatePassword->get('current_password')"
                                                    class="mt-2 text-danger list-unstyled" />
                                            </div>
                                        </div>

                                        <!-- New Password -->
                                        <div class="form-group row">
                                            <label for="password" class="col-sm-2 col-form-label">New Password</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password"
                                                        id="password" autocomplete="new-password"
                                                        placeholder="Enter new password">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary togglePassword"
                                                            type="button" data-target="password">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <x-input-error :messages="$errors->updatePassword->get('password')"
                                                    class="mt-2 text-danger list-unstyled" />
                                            </div>
                                        </div>

                                        <!-- Password Confirmation -->
                                        <div class="form-group row">
                                            <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm
                                                Password</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="password" class="form-control"
                                                        name="password_confirmation" id="password_confirmation"
                                                        autocomplete="new-password" placeholder="Confirm password">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary togglePassword"
                                                            type="button" data-target="password_confirmation">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')"
                                                    class="mt-2 text-danger list-unstyled" />
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-danger">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
