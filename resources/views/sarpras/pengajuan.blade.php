@extends('layouts.admin')
@section('adminContainer')
  <div class="row">
    <div class="col-4">
      <div class="card">
        <div class="card-header py-3">
          <div class="d-flex flex-column">
            <h5 class="mb-0">Riwayat Hasil Prediksi {{ $riwayat->first()->tahun->tahun }}</h2>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive mt-3">
            <table class="table align-middle">
              <thead class="table-secondary">
                <tr>
                  <th>No</th>
                  <th>Nama Aset</th>
                  <th>JA</th>
                  <th>JL</th>
                  <th>JTL</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $index = 1;
                @endphp

                @foreach ($riwayat as $item)
                  <tr>
                    <td>{{ $index++ }}</td>
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
    </div>
    <div class="col-3">
      <div class="card">
        <div class="card-header py-3">
          <div class="d-flex flex-column">
            <h5 class="mb-0">Data Terakhir ({{ $lastData->first()->tahun->tahun }})</h2>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive mt-3">
            <table class="table align-middle">
              <thead class="table-secondary">
                <tr>
                  <th>JA</th>
                  <th>JL</th>
                  <th>JTL</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $index = 1;
                @endphp

                @foreach ($lastData as $item)
                  <tr>
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
    </div>
  </div>
  <div class="overlay nav-toggle-icon"></div>
  <!--end footer-->
@endsection
