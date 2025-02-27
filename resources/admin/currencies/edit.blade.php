<x-app-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        Update Currency
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
                        <h1 class="m-0">Update user</h1>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('users.index') }}">Currencies</a></li>
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
                <form action="{{ route('admin.currencies.update', Crypt::encryptString($currency->id)) }}" method="POST" class="row" enctype="multipart/form-data">
                    <div class="col-md-12 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Currency Details</h3>
                                @csrf
                                @method('PATCH')
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" name="name" id="name" value="{{ old('name', $currency->name) }}" class="form-control" placeholder="Enter Name">

                                            @if ($errors->has('name'))
                                                <div class="error text-danger mt-3">{{ $errors->first('name') }}</div>
                                            @endif
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="symbol">Symbol</label>
                                            <input type="text" name="symbol" id="symbol" value="{{ old('symbol', $currency->symbol) }}" class="form-control" placeholder="Enter symbol">

                                            @if ($errors->has('symbol'))
                                                <div class="error text-danger mt-3">{{ $errors->first('symbol') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="currency">Currency Code</label>
                                            <input type="text" name="currency" id="currency" value="{{ old('currency', $currency->currency) }}" class="form-control" placeholder="Enter currency">

                                            @if ($errors->has('currency'))
                                                <div class="error text-danger mt-3">{{ $errors->first('currency') }}</div>
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