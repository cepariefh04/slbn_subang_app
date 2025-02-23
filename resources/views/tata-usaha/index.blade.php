@extends('layouts.notAdmin')
@section('container')
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
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @php
              $index = 1;
            @endphp

            @foreach ($peserta as $item)
              @php
                $tahunAkhir = (int) explode('-', $item->tahun->tahun)[1];
                $isEditable = $tahunAkhir > 2024;
              @endphp
              <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $item->tahun->tahun }}</td>
                <td>{{ $item->laki_laki }}</td>
                <td>{{ $item->perempuan }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>
                  <div class="table-actions d-flex align-items-center gap-3 fs-6">
                    @if ($isEditable)
                      <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                        data-bs-target="#editDataPeserta{{ $item->id }}">
                        <i class="bi bi-pencil-fill"></i> Edit
                      </button>
                    @endif
                  </div>
                </td>
              </tr>

              <div class="modal fade" id="editDataPeserta{{ $item->id }}" tabindex="-1"
                aria-labelledby="editDataPesertaLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="{{ route('TU.updatePeserta', $item->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-header">
                        <h5 class="modal-title" id="editDataPesertaLabel{{ $item->id }}">Edit Jumlah Peserta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label for="updateLakiLaki{{ $item->id }}" class="form-label">Laki-laki</label>
                          <input type="number" class="form-control update-laki" id="updateLakiLaki{{ $item->id }}"
                            name="laki_laki" value="{{ $item->laki_laki }}" min="0" required>
                        </div>
                        <div class="mb-3">
                          <label for="updatePerempuan{{ $item->id }}" class="form-label">Perempuan</label>
                          <input type="number" class="form-control update-perempuan"
                            id="updatePerempuan{{ $item->id }}" name="perempuan" value="{{ $item->perempuan }}"
                            min="0" required>
                        </div>
                        <div class="mb-3">
                          <label for="updateJumlah{{ $item->id }}" class="form-label">Jumlah</label>
                          <input type="number" class="form-control" id="updateJumlah{{ $item->id }}" name="jumlah"
                            value="{{ $item->jumlah }}" required readonly>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update Data</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="overlay nav-toggle-icon"></div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      document.querySelectorAll(".update-laki, .update-perempuan").forEach(function(input) {
        input.addEventListener("input", function() {
          let id = this.id.replace("updateLakiLaki", "").replace("updatePerempuan", "");
          let lakiLaki = document.getElementById("updateLakiLaki" + id).value;
          let perempuan = document.getElementById("updatePerempuan" + id).value;

          // Konversi ke angka dan jumlahkan
          let total = (parseInt(lakiLaki) || 0) + (parseInt(perempuan) || 0);

          // Masukkan ke input jumlah
          document.getElementById("updateJumlah" + id).value = total;
        });
      });
    });
  </script>
@endsection
