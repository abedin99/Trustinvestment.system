<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">{{ Setting::get('app_name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                <span class="text-success">Active</span>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline mt-2">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

                @permit('dashboard')
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : null }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endpermit


                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link {{ Request::routeIs('admin.users.*') ? 'active' : null }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>


                <li class="nav-header">SYSTEM</li>

                @permit([
                    'currency_index',
                    'currency_create',
                    'currency_edit',
                    'currency_show',
                    'currency_delete',
                    'currency_permissions',
                    'currency_force_delete'
                ])
                    @permit('currency_index')
                        <li class="nav-item">
                            <a href="{{ route('admin.currencies.index') }}"
                                class="nav-link {{ Request::routeIs('admin.currencies.*') ? 'active' : null }}">
                                <i class="nav-icon fas fa-dollar-sign"></i>
                                <p>Currency</p>
                            </a>
                        </li>
                    @endpermit
                @endpermit


                <li class="nav-item">
                    <a href="{{ route('admin.admins.index') }}"
                        class="nav-link {{ Request::routeIs('admin.admins.*') ? 'active' : null }}">
                        <i class="nav-icon fa fa-solid fa-user-tie"></i>
                        <p>Admins</p>
                    </a>
                </li>

                @permit([
                    'role_index',
                    'role_create',
                    'role_edit',
                    'role_show',
                    'role_delete',
                    'role_permissions',
                    'role_force_delete'
                ])
                    @permit('role_index')
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}"
                                class="nav-link {{ Request::routeIs('admin.roles.*') ? 'active' : null }}">
                                <i class="nav-icon fa fas fa-certificate"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                    @endpermit
                @endpermit
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
