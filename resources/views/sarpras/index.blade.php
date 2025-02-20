@extends('layouts.notAdmin')
@section('container')
  <form action="{{ route('sarpras.updateAset') }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card">
      <div class="card-header py-3">
        <div class="d-flex align-items-center justify-content-between">
          <h2 class="mb-0">Daftar Aset</h2>
          @if (collect($assets)->contains(function ($item) {
                  return (int) explode('-', $item->tahun->tahun)[1] > 2024;
              }))
            <div>
              <button type="button" class="btn btn-warning" id="edit-btn"><i class="bi bi-pencil-square"></i> Edit</button>
              <button type="button" class="btn btn-danger d-none" id="cancel-btn"><i class="bi bi-x-lg"></i> Batal</button>
              <button type="submit" class="btn btn-primary d-none" id="save-btn"><i class="bi bi-floppy"></i> Simpan Perubahan</button>
            </div>
          @endif
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
              @php $index = 1; @endphp

              @foreach ($assets as $item)
                @php
                  $tahunAkhir = (int) explode('-', $item->tahun->tahun)[1];
                  $isEditable = $tahunAkhir > 2024;
                @endphp
                <tr data-id="{{ $item->id }}" class="{{ $isEditable ? 'editable' : '' }}">
                  <td>{{ $index++ }}</td>
                  <td id="tahun">{{ $item->tahun->tahun }}</td>
                  <td>{{ $item->aset->nama }}</td>

                  @if ($isEditable)
                    <td class="edit-mode" data-field="jumlah">{{ $item->jumlah }}</td>
                    <td class="edit-mode" data-field="jumlah_layak">{{ $item->jumlah_layak }}</td>
                    <td class="edit-mode" data-field="jumlah_tidak_layak">{{ $item->jumlah_tidak_layak }}</td>
                  @else
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ $item->jumlah_layak }}</td>
                    <td>{{ $item->jumlah_tidak_layak }}</td>
                  @endif
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </form>

  <div class="overlay nav-toggle-icon"></div>
  <!--end footer-->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let editBtn = document.getElementById('edit-btn');
      let saveBtn = document.getElementById('save-btn');
      let cancelBtn = document.getElementById('cancel-btn');

      editBtn.addEventListener('click', function() {
        document.querySelectorAll('.editable .edit-mode').forEach(cell => {
          let value = cell.innerText.trim();
          let field = cell.getAttribute('data-field');
          let id = cell.closest('tr').getAttribute('data-id');

          // Ubah jumlah_layak, jumlah_tidak_layak, dan jumlah_aset menjadi input
          if (field === 'jumlah_layak' || field === 'jumlah_tidak_layak' || field === 'jumlah') {
            cell.innerHTML = `<input type="number" name="${field}[${id}]" value="${value}"
                             class="form-control jumlah-input" data-id="${id}" data-field="${field}">`;
          }
        });

        // Sembunyikan tombol Edit, tampilkan tombol Simpan
        editBtn.classList.add('d-none');
        saveBtn.classList.remove('d-none');
        cancelBtn.classList.remove('d-none');

        // Event listener untuk update jumlah aset jika jumlah_layak atau jumlah_tidak_layak berubah
        document.querySelectorAll('.jumlah-input').forEach(input => {
          input.addEventListener('input', function() {
            let id = this.getAttribute('data-id');
            let jumlahLayak = document.querySelector(`input[name="jumlah_layak[${id}]"]`).value;
            let jumlahTidakLayak = document.querySelector(`input[name="jumlah_tidak_layak[${id}]"]`)
            .value;

            // Hitung jumlah aset jika jumlah_layak atau jumlah_tidak_layak berubah
            if (this.getAttribute('data-field') === 'jumlah_layak' || this.getAttribute('data-field') ===
              'jumlah_tidak_layak') {
              let totalAset = (parseInt(jumlahLayak) || 0) + (parseInt(jumlahTidakLayak) || 0);

              // Update jumlah aset hanya jika jumlah_layak atau jumlah_tidak_layak berubah
              let jumlahAsetInput = document.querySelector(`input[name="jumlah[${id}]"]`);
              jumlahAsetInput.value = totalAset;
            }
          });
        });
      });

      cancelBtn.addEventListener('click', function() {
        location.reload();
      })
    });
  </script>
@endsection
