<!--start sidebar -->
<aside class="sidebar-wrapper" data-simplebar="true">
  <div class="sidebar-header">
    <div>
      <img src="{{ asset('assets/images/logo.png') }}" class="logo-icon" alt="logo icon">
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
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="bi bi-house-fill"></i>
          </div>
          <div class="menu-title">Data Master</div>
        </a>
        <ul>
          <li>
            <a href="/dashboard/sarpras">
              <i class="bi bi-table"></i>
              <div class="menu-title">Data Aset</div>
            </a>
          </li>
          <li>
            <a href="/dashboard/sarpras/data-peserta">
              <i class="bi bi-people"></i>
              <div class="menu-title">Data Peserta Didik</div>
            </a>
          </li>
        </ul>
      </li>
      <li>
        {{-- <a href="/dashboard/sarpras" class="has-arrow"> --}}
        <a href="/dashboard/sarpras/prediksi-aset">
          <div class="parent-icon"><i class="bi bi-calculator"></i>
          </div>
          <div class="menu-title">Prediksi Aset</div>
        </a>
      </li>
      <li>
        <a href="/dashboard/sarpras/pengajuan">
          <div class="parent-icon"><i class="bi bi-door-open"></i>
          </div>
          <div class="menu-title">Rekap Data Pengajuan</div>
        </a>
      </li>
    @else
      <li>
        {{-- <a href="/dashboard/sarpras" class="has-arrow"> --}}
        <a href="/dashboard/tata-usaha">
          <div class="parent-icon"><i class="bi bi-grid-fill"></i>
          </div>
          <div class="menu-title">Data Master</div>
        </a>
      </li>
      <li>
        {{-- <a href="/dashboard/sarpras" class="has-arrow"> --}}
        <a href="/dashboard/prediksi-peserta">
          <div class="parent-icon"><i class="bi bi-calculator"></i>
          </div>
          <div class="menu-title">Prediksi Peserta</div>
        </a>
      </li>
    @endif
  </ul>
  <!--end navigation-->
</aside>
<!--end sidebar -->
