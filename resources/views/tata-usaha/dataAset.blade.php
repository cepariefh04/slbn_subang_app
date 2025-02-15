@extends('layouts.notAdmin')
@section('container')
  <div class="card">
    <div class="card-header py-3">
      <div class="d-flex flex-column">
        <h2 class="mb-0">Daftar Aset</h2>
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

      <div class="table-responsive">
        <table id="example2" class="table table-striped table-bordered">
          <thead>
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
          <tfoot>
            <tr>
              <th>No</th>
              <th>Tahun</th>
              <th>Nama Aset</th>
              <th>Jumlah Aset</th>
              <th>Jumlah Layak</th>
              <th>Jumlah Tidak Layak</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  <div class="overlay nav-toggle-icon"></div>
@endsection
