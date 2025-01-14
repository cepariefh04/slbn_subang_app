<!--start sidebar -->
<aside class="sidebar-wrapper" data-simplebar="true">
  <div class="sidebar-header">
    <div>
      <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
    </div>
    <div>
      <h4 class="logo-text">SLBN Subang</h4>
    </div>
    <div class="toggle-icon ms-auto"><i class="bi bi-list"></i>
    </div>
  </div>
  <!--navigation-->
  <ul class="metismenu" id="menu">
    @if (Auth::user()->level === 'Admin')
      <li>
        <a href="/dashboard">
          <div class="parent-icon"><i class="bi bi-house-fill"></i>
          </div>
          <div class="menu-title">Dashboard</div>
        </a>
      </li>
    @elseif (Auth::user()->level === 'SarPras')
      <li>
        {{-- <a href="/dashboard/sarpras" class="has-arrow"> --}}
        <a href="/dashboard/sarpras">
          <div class="parent-icon"><i class="bi bi-grid-fill"></i>
          </div>
          <div class="menu-title">Data Master</div>
        </a>
      </li>
      <li>
        {{-- <a href="/dashboard/sarpras" class="has-arrow"> --}}
        <a href="/dashboard/prediksi-aset">
          <div class="parent-icon"><i class="bi bi-calculator"></i>
          </div>
          <div class="menu-title">Prediksi Aset</div>
        </a>
      </li>
    @endif
  </ul>
  <!--end navigation-->
</aside>
<!--end sidebar -->
