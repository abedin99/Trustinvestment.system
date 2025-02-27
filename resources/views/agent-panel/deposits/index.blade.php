<x-app-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        User Dashboard
    </x-slot>

    <x-slot name="css">

    </x-slot>

    <x-slot name="js">
    </x-slot>

    <x-slot name="header_components">

    </x-slot>

    <x-slot name="footer_components">
        <script src="{{ asset('/') }}plugins/chart.js/Chart.min.js"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="{{ asset('/') }}dist/js/pages/dashboard3.js"></script>
    </x-slot>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    @if (isset($total_deposits) && $total_deposits > 0)
                        <h4 class="text-danger m-3">Total Deposit: {{ $total_deposits }}</h4>
                    @endif
                    @if (isset($total_receivable_amount) && $total_receivable_amount > 0)
                        <h4 class="text-danger m-3">Total Receivable Amount:
                            {{ $general->cur_sym }}{{ formatter_money($total_receivable_amount) }}</h4>
                    @endif

                    <div class="table-responsive table-responsive-xl">
                        <table class="table align-items-center table-light">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Deposit Code</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Deposit Method</th>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Charge</th>
                                    <th scope="col">Receivable Amount</th>
                                    <th scope="col">In Method Currency</th>
                                    <th scope="col">Status</th>
                                    @if (request()->routeIs('admin.deposit.pending'))
                                        <th scope="col">Action</th>
                                    @else
                                        <th scope="col">Details</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>{{ show_datetime($deposit->created_at) }}</td>
                                        <td class="font-weight-bold text-uppercase">{{ $deposit->trx }}</td>
                                        <td><span class="text-info">{{ $deposit->user->username }}</span></td>
                                        <td>{{ $deposit->gateway->name }}</td>
                                        <td class="text-primary">{{ $general->cur_text }}
                                            {{ formatter_money($deposit->amount + $deposit->charge) }}</td>
                                        <td class="text-danger">{{ $general->cur_text }}
                                            {{ formatter_money($deposit->charge) }}</td>
                                        <td class="text-success">{{ $general->cur_text }}
                                            {{ formatter_money($deposit->amount) }}</td>
                                        <td>{{ $deposit->method_currency }} {{ formatter_money($deposit->final_amo) }}
                                        </td>
                                        <td>
                                            @if ($deposit->status == 2)
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($deposit->status == 1)
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($deposit->status == 3)
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        @if (request()->routeIs('user.deposits.pending'))
                                            <td>
                                                <button class="btn btn-success approveBtn"
                                                    data-id="{{ Crypt::encryptString($deposit->id) }}"
                                                    data-amount="{{ $general->cur_text }}{{ formatter_money($deposit->amount) }}"
                                                    data-username="{{ $deposit->user->username }}"><i
                                                        class="fa fa-fw fa-check"></i></button>
                                                <button class="btn btn-danger rejectBtn"
                                                    data-id="{{ Crypt::encryptString($deposit->id) }}"
                                                    data-amount="{{ $general->cur_text }}{{ formatter_money($deposit->amount) }}"
                                                    data-username="{{ $deposit->user->username }}"><i
                                                        class="fa fa-fw fa-ban"></i></button>
                                                <button class="btn btn-info viewBtn"
                                                    data-img="{{ asset(config('constants.deposit.verify.path') . '/' . $deposit->verify_image) }}"
                                                    data-detail="{{ json_encode($deposit->detail) }}"><i
                                                        class="fa fa-fw fa-eye"></i></button>
                                            </td>
                                        @else
                                            <td>
                                                <button class="btn btn-info viewBtn"
                                                    data-img="{{ asset(config('constants.deposit.verify.path') . '/' . $deposit->verify_image) }}"
                                                    data-detail="{{ json_encode($deposit->detail) }}"><i
                                                        class="fa fa-fw fa-eye"></i></button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ $empty_message }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-4">
                        <nav aria-label="...">
                            {{ $deposits->appends($_GET)->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->

    {{-- VIEW MODAL --}}
    <div id="viewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View User Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-5"><img src="" class="verify_image"></div>
                        <div class="col-md-12">
                            <table class="table table-bordered">

                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Deposit Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('deposits.approve') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>Are you sure to <span class="font-weight-bold">approve</span> <span
                                class="font-weight-bold withdraw-amount text-success"></span> deposit of <span
                                class="font-weight-bold withdraw-user"></span>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Approve</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Deposit Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('deposits.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>Are you sure to <span class="font-weight-bold">reject</span> <span
                                class="font-weight-bold withdraw-amount text-success"></span> deposit of <span
                                class="font-weight-bold withdraw-user"></span>?</p>

                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea class="form-control" rows="5" name="message" id="message" placeholder="Enter your message">{{ old('message') }} Your transaction information is wrong! Your deposit amount not match to your request. Please deposit to exact amount of currency. Please try again and use your last transaction screenshot and transaction number.</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Reject</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
