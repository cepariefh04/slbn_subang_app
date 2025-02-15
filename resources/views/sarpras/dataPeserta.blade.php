@extends('layouts.notAdmin')
@section('container')
  <div class="card">
    <div class="card-header py-3">
      <div class="d-flex flex-column">
        <h2 class="mb-0">Daftar Peserta Didik</h2>
      </div>
    </div>

    <div class="card-body">
      {{-- ALERT --}}
      @if (session('success'))
        <x-alert type="success" :message="session('success')" />
      @endif

      @if (session('error'))
        <x-alert type="danger" :message="session('error')" />
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
