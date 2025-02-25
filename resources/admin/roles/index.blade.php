<x-app-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        {{ $page_title }}
    </x-slot>

    <x-slot name="css">

    </x-slot>

    <x-slot name="js">
        {!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
        <script type="text/javascript">
            function deleteConfirmation(ele) 
            {
                var id = $(ele).data("id");
                var action_url = $(ele).data("action");
                Swal.fire({
                    icon: 'info',
                    title: "Delete?",
                    text: "Please ensure and then confirm!",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: !0
                }).then(function (e) {
        
                    if (e.value === true) {
                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        
                        $.ajax({
                            type: 'DELETE',
                            url: action_url,
                            data: {_token: CSRF_TOKEN},
                            dataType: 'JSON',
                            success: function (results) {
        
                                if (results.success === true) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: results.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $("#{{ $dataTable->getTableId() }}").DataTable().ajax.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: results.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $("#{{ $dataTable->getTableId() }}").DataTable().ajax.reload();
                                }
                            }
                        });
        
                    } else {
                        e.dismiss;
                    }
        
                }, function (dismiss) {
                    return false;
                })
            }
        </script>
    </x-slot>

    <x-slot name="header_components">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    </x-slot>

    <x-slot name="footer_components">
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
        <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    </x-slot>


    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $page_title }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}"> Manage Role</a></li>
                        <li class="breadcrumb-item active">Index</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">{{ $page_title }}</div>
                            @permit('role_create')
                                <div class="card-tools">
                                    <a href="{{ route('admin.roles.create') }}" class="btn btn-block bg-gradient-primary btn-sm"><i class="fas fa-plus mr-2"></i> Add</a>
                                </div>
                            @endpermit
                        </div>
                        <div class="card-body">

                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</x-app-layout>