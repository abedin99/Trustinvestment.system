<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-logo">
            <div class="peers ai-c fxw-nw">
                <div class="peer peer-greed">
                    <a class="sidebar-link td-n" href="/demo/">
                        <div class="peers ai-c fxw-nw">
                            <div class="peer">
                                <div class="logo"><img src="assets/static/images/logo.png" alt=""></div>
                            </div>
                            <div class="peer peer-greed">
                                <h5 class="lh-1 mB-0 logo-text">{{ config('app.name') }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="peer">
                    <div class="mobile-toggle sidebar-toggle"><a href="" class="td-n"><i
                                class="ti-arrow-circle-left"></i></a></div>
                </div>
            </div>
        </div>
        <ul class="sidebar-menu scrollable pos-r">
            <li class="nav-item mT-30 {{ Request::routeIs('dashboard') ? 'actived' : null }}">
                <a class="sidebar-link" href="{{ route('dashboard') }}"><span class="icon-holder">
                        <i class="c-blue-500 fa-solid fa-gauge"></i>
                    </span><span class="title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link" href="javascript:void(0);">
                    <span class="icon-holder"><i class="c-brown-500 fa-solid fa-hourglass-start"></i></span>
                    <span class="title">My Packages</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link" href="javascript:void(0);">
                    <span class="icon-holder"><i class="c-brown-500 fa-solid fa-cubes"></i></span>
                    <span class="title">Packages</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link" href="#">
                    <span class="icon-holder"><i class="c-deep-orange-500 fa-solid fa-hand-holding-dollar"></i></span>
                    <span class="title">Deposit</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link" href="#">
                    <span class="icon-holder"><i class="c-deep-purple-500 fa-solid fa-money-bill-transfer"></i> </span>
                    <span class="title">Withdraw</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link" href="#">
                    <span class="icon-holder"><i class="c-indigo-500 fa-solid fa-receipt"></i> </span>
                    <span class="title">My Transaction</span>
                </a>
            </li>
        </ul>
    </div>
</div>
