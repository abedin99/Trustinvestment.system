<x-app-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        {{ $page_title }}
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

                //Date picker
                $('#dob').datetimepicker({
                    format: 'L'
                });

                //Initialize Select2 Elements
                $('.select2').select2();

                //Initialize Select2 Elements
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                });
            });
        </script>
        
    </x-slot>

    <x-slot name="header_components">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('/') }}plugins/select2/css/select2.min.css">
        <link rel="stylesheet" href="{{ asset('/') }}plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    </x-slot>

    <x-slot name="footer_components">
        <!-- bootstrap  switch -->
        <script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset('/') }}plugins/select2/js/select2.full.min.js"></script>
        <!-- InputMask -->
        <script src="{{ asset('/') }}plugins/moment/moment.min.js"></script>
        <script src="{{ asset('/') }}plugins/inputmask/jquery.inputmask.min.js"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="{{ asset('/') }}plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    </x-slot>


    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $page_title }}</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}"> Manage Role</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('admin.roles.store') }}" method="POST" class="row justify-content-center" enctype="multipart/form-data">
                <div class="col-md-8 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $page_title }} Details</h3>
                            @csrf
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name') }}" id="name" placeholder="Enter name">
                                @if ($errors->has('name'))
                                    <div class="error text-danger">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <br>
                                <input type="checkbox" name="status" id="status" @checked(old('status')) data-bootstrap-switch data-off-color="danger" data-on-color="success">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="start" class="btn btn-primary">Submit</button>
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