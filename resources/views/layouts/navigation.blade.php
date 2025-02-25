<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
        </li>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
          <a href="{{ route('dashboard') }}" class="nav-link dropdown-toggle" data-toggle="dropdown">
              <i class="fas fa-user"></i>
          </a>
          <div class="dropdown-menu">
              <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
              <a class="dropdown-item" href="{{ route('password.edit') }}">Change Password</a>
              <!-- Authentication -->
              <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="button" class="dropdown-item"
                      onclick="event.preventDefault();
                                  this.closest('form').submit();">
                      {{ __('Log Out') }}
                  </button>
              </form>
          </div>
      </li>
    </ul>
</nav>
<!-- /.navbar -->