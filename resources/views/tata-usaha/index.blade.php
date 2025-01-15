@extends('layouts.admin')
@section('adminContainer')
  <div class="card">
    <div class="card-header py-3">
      <div class="d-flex flex-column">
        <h2 class="mb-0">Daftar Peserta Didik</h2>
        <h5 class="mb-0">Dari tahun 2019 - 2024</h5>
      </div>

    </div>

    <div class="card-body">
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
              <th>Laki-laki</th>
              <th>Perempuan</th>
              <th>Jumlah</th>
            </tr>
          </thead>
          <tbody>
            @php
              $index = 1;
            @endphp

            @foreach ($peserta as $item)
              <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $item->tahun->tahun }}</td>
                <td>{{ $item->laki_laki }}</td>
                <td>{{ $item->perempuan }}</td>
                <td>{{ $item->jumlah }}</td>
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
