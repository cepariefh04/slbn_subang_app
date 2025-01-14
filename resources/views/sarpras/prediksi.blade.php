@extends('layouts.admin')
@section('adminContainer')
  <div class="card">
    <div class="card-header py-3">
      <div class="d-flex flex-column">
        <h2 class="mb-0">Prediksi Aset</h2>
        <h5 class="mb-0">Tahun Ajaran 2024-2025</h5>
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
      <button class="btn btn-primary">Mulai Prediksi</button>
      <div class="table-responsive mt-3">
        <table class="table align-middle">
          <thead class="table-secondary">
            <tr>
              <th>No</th>
              <th>Nama Aset</th>
              <th>Hasil Prediksi</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @php
              $index = 1;
            @endphp

            @foreach ($assets as $item)
              <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $item->nama }}</td>
                <td>1</td>
                <td>1</td>
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
