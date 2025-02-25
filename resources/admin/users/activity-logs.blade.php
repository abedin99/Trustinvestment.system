<x-app-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        {{ $page_title }}
    </x-slot>

    <x-slot name="css">

    </x-slot>

    <x-slot name="js">
        <!-- Page specific script -->
        <script>
            $(function() {
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
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    </x-slot>

    <x-slot name="footer_components">
        <!-- Select2 -->
        <script src="{{ asset('/') }}plugins/select2/js/select2.full.min.js"></script>
        <!-- InputMask -->
        <script src="{{ asset('/') }}plugins/moment/moment.min.js"></script>
        <script src="{{ asset('/') }}plugins/inputmask/jquery.inputmask.min.js"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="{{ asset('/') }}plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
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
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                        <li class="breadcrumb-item active">{{ $page_title }}</li>
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
                    <form action="{{ route('admin.users.activity-logs', Crypt::encryptString($user->id)) }}" method="GET" class="card">
                        <div class="card-header bg-light">
                            <i class="fa fa-filter" aria-hidden="true"></i> Filter
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-3 col-sm-2 col-12">
                                    <label>Start Date *</label>
                                    <input type="date" class="form-control datepicker" name="sdate"
                                        value="{{ request('sdate') }}" id="sdate" va>
                                </div>

                                <div class="form-group col-md-3 col-sm-2 col-12">
                                    <label>End Date *</label>
                                    <input type="date" class="form-control datepicker" name="edate"
                                        value="{{ request('edate') }}" id="edate" va>
                                </div>

                                <div class="col-md-12 col-sm-3 col-12"></div>

                                <div class="form-group col-md-3 col-sm-3 col-12">
                                    <label>Event</label>
                                    <select name="event" class="form-control select2" id="event">
                                        <option value="All" hidden>All</option>
                                        @foreach ($events as $key => $event)
                                            <option value="{{ $event }}" @selected(request('event')==$event)>{{ Str::headline($event) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @isset($subjects)
                                    <div class="form-group col-md-3 col-sm-3 col-12">
                                        <label>Subject</label>
                                        <select name="subject" class="form-control select2" id="subject">
                                            <option value="All" hidden>All</option>
                                            @foreach ($subjects as $key => $subject)
                                                <option value="{{ class_basename($subject) }}" @selected(request('subject') == class_basename($subject))>{{ Str::headline(class_basename($subject)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endisset
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <div class="card-title"><i class="fas fa-user-clock"></i> Activity Logs</div>
                        </div>
                        <div class="card-body">
                            @foreach ($activity_logs as $log)
                                <div class="card text-left">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ Str::headline($log->event) }} </h4>

                                        <div class="card-tools">
                                            <small>{{ $log->created_at->format('F d, Y h:i:s A') }}</small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">{!! $log->description !!}</p>
                                        @if ($log->changes)
                                            <div class="row">
                                                @if ($log->changes && isset($log->changes['attributes']))
                                                    <div class="col">
                                                        <div class="bg-success p-3">
                                                            <p class="m-0">[attributes] => Array (</p>
                                                            @foreach ($log->changes['attributes'] as $key => $attribute)
                                                                @if (!$attribute)
                                                                    <p class="ml-4 m-0">[{{ $key }}] =>
                                                                        {{ $attribute }}</p>
                                                                @endif
                                                            @endforeach
                                                            <p class="m-0">)</p>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($log->changes && isset($log->changes['old']))
                                                    <div class="col">
                                                        <div class="bg-danger p-3">
                                                            <p class="m-0">[old] => Array (</p>
                                                            @foreach ($log->changes['old'] as $key => $attribute)
                                                                @if (!$attribute)
                                                                    <p class="ml-4 m-0">[{{ $key }}] =>
                                                                        {{ $attribute }}</p>
                                                                @endif
                                                            @endforeach
                                                            <p class="m-0">)</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        @if ($log->subject_id)
                                            @php
                                                $subject = $log->subject_type::find($log->subject_id);
                                            @endphp
                                            @if ($subject->post && $subject->post->title)
                                                <p class="text-success">Post: <a href="{{ route('admin.posts.show', Crypt::encryptString($subject->post->id)) }}">{{ $subject->post->title }}</a></p>
                                            @endif
                                            @if ($subject && $subject->title)
                                                <p class="text-success">Post: <a href="{{ route('admin.posts.show', Crypt::encryptString($subject->id)) }}">{{ $subject->title }}</a></p>
                                            @endif
                                        @endif
                                        <p class="text-danger">Author:
                                            {{ \App\Models\User::find($log->causer_id)->name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($activity_logs->hasPages())
                            <div class="card-footer">
                                {!! $activity_logs->appends(request()->all())->links() !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</x-app-layout>
