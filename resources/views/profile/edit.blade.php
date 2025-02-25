<x-app-layout>

    <!-- Meta Title -->
    <x-slot name="meta_title">
        My Profile
    </x-slot>

    <x-slot name="css">

    </x-slot>

    <x-slot name="js">
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
                    <h1 class="m-0">My Profile</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
 
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                {{-- @include('profile.partial.aside') --}}
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#settings"
                                        data-toggle="tab">Profile</a></li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="settings">
                                    <form class="form-horizontal" action="{{ route('profile.update') }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <!-- Validation Errors -->
                                        <x-validation-errors class="mb-4 text-danger" :errors="$errors" />

                                        <div class="form-group row">
                                            <label for="name" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ old('name', $user->name) }}" id="name"
                                                    placeholder="Name">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" name="email"
                                                    value="{{ old('email', $user->email) }}" id="email"
                                                    placeholder="Email" required>
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