<header class="top-header">
  <nav class="navbar navbar-expand gap-3">
    <div class="mobile-toggle-icon fs-3 d-flex d-lg-none">
      <i class="bi bi-list"></i>
    </div>
    <div class="top-navbar-right ms-auto">
      <ul class="navbar-nav align-items-center gap-1">
        <li class="nav-item search-toggle-icon d-flex d-lg-none">
          <a class="nav-link" href="javascript:;">
            <div class="">
              <i class="bi bi-search"></i>
            </div>
          </a>
        </li>
        {{-- <li class="nav-item dark-mode d-none d-sm-flex">
          <a class="nav-link dark-mode-icon" href="javascript:;">
            <div class="">
              <i class="bi bi-moon-fill"></i>
            </div>
          </a>
        </li> --}}
      </ul>
    </div>
    <div class="dropdown dropdown-user-setting">
      <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
        <div class="user-setting d-flex align-items-center gap-3">
          <img
            src="{{ Auth::user()->photo ? asset(config('app.storage_path') . Auth::user()->photo) : 'assets/images/avatars/avatar-1.png' }}"
            class="user-img" alt="">
          <div class="d-none d-sm-block">
            <p class="user-name mb-0">{{ Auth::user()->name }}</p>
            <small class="mb-0 dropdown-user-designation">{{ Auth::user()->level }}</small>
          </div>
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="dropdown-item" type="submit">
              <div class="d-flex align-items-center">
                <div class=""><i class="bi bi-lock-fill"></i></div>
                <div class="ms-3"><span>Logout</span></div>
              </div>
            </button>
          </form>
        </li>
      </ul>
    </div>
  </nav>
</header>
