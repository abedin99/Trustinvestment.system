<x-app-layout>
    <!-- Meta Title -->
    <x-slot name="meta_title">
        User Deposit
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
                    <h1 class="m-0">Deposit</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Deposit</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="row">

            @foreach ($gatewayCurrency as $data)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <div class="card text-center">
                        <img src="{{ $data->methodImage() }}" class="d-block m-auto w-50" alt="image">
                        <div class="card-body">
                            <h5 class="card-title">{{ __($data->name) }}</h5>
                            <hr>
                            {{-- @if (Auth::user()->agent) --}}
                            <a href="#" data-toggle="modal" data-currency="{{ $data->currency }}"
                                data-min_amount="{{ formatter_money($data->min_amount) }} "
                                data-max_amount=" {{ formatter_money($data->max_amount) }} "
                                data-fcharge=" {{ formatter_money($data->fixed_charge) }}"
                                data-pcharge="{{ formatter_money($data->percent_charge) }} %"
                                data-method_code="{{ $data->method_code }}"
                                class="btn btn-primary  deposit">@lang('Deposit Now')</a>
                            {{-- @endif --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>



    </section>
    <!-- /.content -->

    <!-- Modal -->
    <div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content blue-bg ">
                <div class="modal-header">

                    <h5 class="modal-title" id="exampleModalLabel" style="color: black">@lang('Choose amount')</h5>
                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('deposit.insert') }}" method="POST">
                    @csrf
                    <div class="modal-body text-center">
                        <input type="hidden" name="currency" class="edit-currency" value="">
                        <input type="hidden" name="method_code" class="edit-method-code" value="">




                        <strong style="color: black">@lang('Limit')
                            :<span class="min_amount"></span> -
                            <span class="max_amount"></span> {{ $general->cur_text }} </strong>

                        <div class="form-group text-left">
                            <label>@lang('Amount')</label>


                            <div class="input-group mb-2">

                                <input type="text" class="form-control" name="amount" value="{{ old('amount') }}">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">{{ $general->cur_text }}</div>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="wallet">@lang('Wallet')</label>
                            <select class="form-control" id="wallet" name="wallet" required>
                                <option value="" hidden>Select a wallet</option>
                                @if ($general->deposit_income_balance)
                                    <option value="{{ Crypt::encryptString('income_balance') }}">Income balance
                                    </option>
                                @endif

                                @if ($general->deposit_self_wallet)
                                    <option value="{{ Crypt::encryptString('self_wallet') }}">Self wallet</option>
                                @endif

                                @if ($general->deposit_office_wallet)
                                    <option value="{{ Crypt::encryptString('office_wallet') }}">Office wallet</option>
                                @endif

                                @if ($general->deposit_ecommerce_wallet)
                                    <option value="{{ Crypt::encryptString('ecommerce_wallet') }}">Ecommerce wallet
                                    </option>
                                @endif

                                @if ($general->deposit_earning_wallet)
                                    <option value="{{ Crypt::encryptString('earning_wallet') }}">Daily ncome wallet
                                    </option>
                                @endif

                                @if ($general->deposit_savings_wallet)
                                    <option value="{{ Crypt::encryptString('savings_wallet') }}">Savings Wallet
                                    </option>
                                @endif
                            </select>
                        </div>


                        <strong style="color: black;"> @lang('Charge') :{{ $general->cur_text }} <span
                                class="fcharge"></span> -
                            <span class="pcharge"></span> </strong>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
