<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-logo">
            <div class="peers ai-c fxw-nw">
                <div class="peer peer-greed">
                    <a class="sidebar-link td-n" href="{{ route('admin.dashboard') }}">
                        <div class="peers ai-c fxw-nw">
                            <div class="peer">
                                <div class="logo"><img src="{{ asset('assets/static/images/logo.png') }}"
                                        alt=""></div>
                            </div>
                            <div class="peer peer-greed">
                                <h5 class="lh-1 mB-0 logo-text">{{ config('app.name') }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="peer">
                    <div class="mobile-toggle sidebar-toggle">
                        <a href="" class="td-n">
                            <i class="ti-arrow-circle-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <ul class="sidebar-menu scrollable pos-r">
            <li class="nav-item mT-30 {{ Request::routeIs('admin.dashboard') ? 'actived' : null }}">
                <a class="sidebar-link" href="{{ route('admin.dashboard') }}">
                    <span class="icon-holder">
                        <i class="c-blue-500 fa-solid fa-gauge"></i>
                    </span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('admin.packages.*') ? 'actived' : null }}">
                <a class="sidebar-link" href="{{ route('admin.packages.index') }}">
                    <span class="icon-holder"><i class="c-brown-500 fa-solid fa-cubes"></i></span>
                    <span class="title">Packages</span>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('admin.users.*') ? 'actived' : null }}">
                <a class="sidebar-link" href="{{ route('admin.users.index') }}">
                    <span class="icon-holder"><i class="c-blue-500 fa-solid fa-users"></i> </span>
                    <span class="title">Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link" href="#">
                    <span class="icon-holder"><i class="c-deep-orange-500 fa-solid fa-hand-holding-dollar"></i></span>
                    <span class="title">Deposit Method</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link" href="#">
                    <span class="icon-holder"><i class="c-deep-purple-500 fa-solid fa-money-bill-transfer"></i> </span>
                    <span class="title">Withdraw Method</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link" href="#">
                    <span class="icon-holder"><i class="c-indigo-500 fa-solid fa-receipt"></i> </span>
                    <span class="title">Transaction</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="dropdown-toggle" href="javascript:void(0);">
                    <span class="icon-holder"><i class="c-light-blue-500 fa-solid fa-flag"></i></span>
                    <span class="title">Reports</span>
                    <span class="arrow"><i class="fa-solid fa-angle-right"></i></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="sidebar-link" href="#">Profit And Loss</a></li>
                    <li><a class="sidebar-link" href="#">User DP Report </a></li>
                    <li><a class="sidebar-link" href="#">Sell Report</a></li>
                    <li><a class="sidebar-link" href="#">Balance Report</a></li>
                    <li><a class="sidebar-link" href="#">Deposit Report</a></li>
                    <li><a class="sidebar-link" href="#">Withdrawal Report</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="sidebar-link" href="#">
                    <span class="icon-holder"><i class="c-pink-500 fa-solid fa-headset"></i> </span>
                    <span class="title">Support Ticket</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="dropdown-toggle" href="javascript:void(0);">
                    <span class="icon-holder"><i class="c-red-500 fa-solid fa-gears"></i></span>
                    <span class="title">Settings</span>
                    <span class="arrow"><i class="fa-solid fa-angle-right"></i></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="sidebar-link" href="#">Configurations</a></li>
                    <li><a class="sidebar-link" href="#">User DP Setting</a></li>
                    <li><a class="sidebar-link" href="#">Currencies</a></li>
                    <li><a class="sidebar-link" href="#">Language</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
