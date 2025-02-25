<x-app-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        Update admin
    </x-slot>

    <x-slot name="css">
        
    </x-slot>

    <x-slot name="js">
        <!-- Page specific script -->
        <!-- Bootstrap Switch -->
        <script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
        <script>
            $(function() {
                $("input[data-bootstrap-switch]").each(function() {
                    $(this).bootstrapSwitch('state', $(this).prop('checked'));
                })
            });
        </script>
    </x-slot>

    <x-slot name="header_components">
        <!-- dropzonejs -->
        <link rel="stylesheet" href="{{ asset('/vendor/jQuery-File-Upload/css/jquery.fileupload.css') }}">
    </x-slot>

    <x-slot name="footer_components">
        <!-- bs-custom-file-input -->
        <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
        <!-- dropzonejs -->
        <script src="{{ asset('/vendor/jQuery-File-Upload/js/jquery.fileupload.js') }}"></script>
    </x-slot>

        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Update admin</h1>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.admins.index') }}">Admins</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form action="{{ route('admin.admins.update', Crypt::encryptString($admin->id)) }}" method="POST" class="row" enctype="multipart/form-data">
                    <div class="col-md-12 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Admin Details</h3>
                                @csrf
                                @method('PATCH')
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                            
                                            <label for="role">Role</label>
                                            <select class="form-control" name="role" id="role">
                                                <option value="" hidden>Select one</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->slug }}" @if(old('role') == $role->slug or $admin->roles && $admin->roles->first() && ($admin->roles->first()->slug == $role->slug)) selected @endif>{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('role'))
                                                <div class="error text-danger mt-3">{{ $errors->first('role') }}</div>
                                            @endif
                                        </div>
    
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" class="form-control" placeholder="Enter Name">

                                            @if ($errors->has('name'))
                                                <div class="error text-danger mt-3">{{ $errors->first('name') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="username" id="username" value="{{ old('username', $admin->username) }}" class="form-control" placeholder="Enter username">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-admin"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($errors->has('username'))
                                                <div class="error text-danger">{{ $errors->first('username') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <div class="input-group mb-3">
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" placeholder="Enter email">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-envelope"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($errors->has('email'))
                                                <div class="error text-danger">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" name="password" id="password" class="form-control"
                                                    placeholder="Password">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-lock"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($errors->has('password'))
                                                <div class="error text-danger">{{ $errors->first('password') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" name="password_confirmation" id="password_confirmation"
                                                    class="form-control" placeholder="Confirm Password">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-lock"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="banned_at">Banned until</label>
                                            @if ($admin->banned_at)
                                                <br><span class="badge badge-danger">Banned</span><br><br>
                                            @endif
                                            <div class="input-group mb-3">
                                                <input type="date" name="banned_at" id="banned_at" value="{{ old('banned_at', (($admin->banned_at)? $admin->banned_at->format('Y-m-d'):null)) }}" class="form-control" placeholder="Enter ban date">
                                            </div>
                                            @if ($errors->has('banned_at'))
                                                <div class="error text-danger">{{ $errors->first('banned_at') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="disabled_at">Disabled <span class="text-danger">*</span></label>
                                            <br>
                                            <input type="checkbox" name="disabled_at" id="disabled_at" @if(old('disabled_at', $admin->disabled_at)) checked @endif data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                            @if ($errors->has('disabled_at'))
                                                <div class="error text-danger">{{ $errors->first('disabled_at') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="start" class="btn btn-primary">update</button>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </form>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
</x-app-layout>