<header class="top-header">
  <nav class="navbar gap-3">
    <div class="d-flex align-items-center gap-3">
      <a href="/admin">
        <img src="{{ asset('assets/images/logo.png') }}" class="logo-icon" width="35" alt="logo icon">
      </a>
      <a href="/admin" style="color:#5e636b;">
        <h4 class="logo-text">SLBN Subang</h4>
      </a>
    </div>
    <div class="dropdown dropdown-user-setting">
      <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
        <div class="user-setting d-flex align-items-center gap-3">
          <img src="{{ asset('assets/images/avatars/user.png') }}" class="user-img" alt="">
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
