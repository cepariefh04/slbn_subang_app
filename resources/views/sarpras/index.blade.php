@extends('layouts.admin')
@section('adminContainer')
  <div class="card">
    <div class="card-header py-3">
      <div class="d-flex flex-column">
        <h2 class="mb-0">Daftar Aset</h2>
        <h5 class="mb-0">Tahun Ajaran {{ $selectedYear }}</h5>
      </div>

    </div>

    <div class="card-body">
      <form action="{{ route('sarpras.dashboard') }}" method="GET">
        <div class="row g-3">
          <div class="col-lg-2 col-6 col-md-3">
            <select class="form-select" name="year" onchange="this.form.submit()">
              <option value="" disabled {{ is_null($selectedYear) ? 'selected' : '' }}>Semua Tahun</option>
              @foreach ($years as $year)
                <option value="{{ $year->tahun }}" {{ $selectedYear == $year->tahun ? 'selected' : '' }}>
                  {{ $year->tahun }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-2 col-6 col-md-3">
            <select class="form-select" name="show" onchange="this.form.submit()">
              <option value="10" {{ $selectedShow == 10 ? 'selected' : '' }}>Lihat 10</option>
              <option value="30" {{ $selectedShow == 30 ? 'selected' : '' }}>Lihat 30</option>
              <option value="50" {{ $selectedShow == 50 ? 'selected' : '' }}>Lihat 50</option>
              <option value="all" {{ $selectedShow == 'all' ? 'selected' : '' }}>Lihat Semua</option>
            </select>
          </div>
        </div>
      </form>

      {{-- ALERT --}}
      @if (session('success'))
        <div class="alert border-0 bg-light-success alert-dismissible fade show py-2 mt-4" id="successAlert">
          <div class="d-flex align-items-center">
            <div class="fs-3 text-success">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="ms-3">
              <div class="text-success">{{ session('success') }}</div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if (session('error'))
        <div class="alert border-0 bg-light-danger alert-dismissible fade show py-2 mt-4" id="errorAlert">
          <div class="d-flex align-items-center">
            <div class="fs-3 text-danger">
              <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <div class="ms-3">
              <div class="text-danger">{{ session('error') }}</div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      {{-- END ALERT --}}

      <div class="table-responsive mt-3">
        <table class="table align-middle">
          <thead class="table-secondary">
            <tr>
              <th>No</th>
              <th>Tahun</th>
              <th>Nama Aset</th>
              <th>Jumlah Aset</th>
              <th>Jumlah Layak</th>
              <th>Jumlah Tidak Layak</th>
            </tr>
          </thead>
          <tbody>
            @php
              $index = 1;
            @endphp

            @foreach ($assets as $item)
              <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $item->tahun->tahun }}</td>
                <td>{{ $item->aset->nama }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->jumlah_layak }}</td>
                <td>{{ $item->jumlah_tidak_layak }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>


  <div class="overlay nav-toggle-icon"></div>
  <!--end footer-->
@endsection
