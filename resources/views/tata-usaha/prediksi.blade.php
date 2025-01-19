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
      <button id="predictButton" class="btn btn-primary">Mulai Prediksi</button>
      <button id="saveButton" disabled class="btn btn-success">Simpan Hasil Prediksi</button>
      <div class="table-responsive mt-3">
        <table class="table align-middle" id="assetsTable">
          <thead class="table-secondary">
            <tr>
              <th>No</th>
              <th>Laki-laki</th>
              <th>Perempuan</th>
              <th>Jumlah</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td class="prediction-result">Menunggu...</td>
              <td class="prediction-result">Menunggu...</td>
              <td class="prediction-result">Menunggu...</td>
              <td class="prediction-result">-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="overlay nav-toggle-icon"></div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#predictButton').on('click', function() {
        if ($(this).text() === 'batal') {
          location.reload(); // Reload halaman
          return; // Hentikan eksekusi lebih lanjut
        }

        // Ubah status tombol saat proses berlangsung
        $(this).prop('disabled', true).text('Memproses...');

        // Lakukan request AJAX
        $.ajax({
          url: "{{ route('TU.prosesPrediksi') }}",
          method: "GET",
          success: function(response) {
            if (response.success) {
              const predictions = response.predictions;

              // Ambil elemen baris pertama dari tabel
              const tableRow = $('#assetsTable tbody tr').first();

              // Perbarui kolom di dalam tabel dengan hasil prediksi
              tableRow.find('td').eq(1).text(predictions.laki_laki[0]); // Laki-laki
              tableRow.find('td').eq(2).text(predictions.perempuan[0]); // Perempuan
              tableRow.find('td').eq(3).text(predictions.total[0]); // Jumlah
              tableRow.find('td').eq(4).text('Berhasil'); // Status
            } else {
              alert('Terjadi kesalahan dalam proses prediksi!');
            }
          },
          error: function() {
            alert('Gagal terhubung ke server.');
          },
          complete: function() {
            // Aktifkan kembali tombol setelah proses selesai
            $('#predictButton').prop('disabled', false).text('Batal');
            $('#saveButton').prop('disabled', false);
          }
        });
      });


      $('#saveButton').on('click', function() {
        $(this).prop('disabled', true).text('Menyimpan...');

        // Ambil data dari tabel
        const tableRow = $('#assetsTable tbody tr').first();
        const lakiLaki = tableRow.find('td').eq(1).text(); // Laki-laki
        const perempuan = tableRow.find('td').eq(2).text(); // Perempuan
        const total = tableRow.find('td').eq(3).text(); // Jumlah

        // Kirim data melalui AJAX
        $.ajax({
          url: "{{ route('TU.simpanPrediksi') }}", // Ganti dengan route untuk menyimpan
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            laki_laki: lakiLaki,
            perempuan: perempuan,
            total: total
          },
          success: function(response) {
            if (response.success) {
              alert('Data berhasil disimpan ke database.');
              $('#saveButton').prop('disabled', true).text('Tersimpan');
              $('#predictButton').hide();
            } else {
              alert('Terjadi kesalahan saat menyimpan data.');
              $('#saveButton').prop('disabled', false).text('Simpan Hasil Prediksi');
            }
          },
          error: function() {
            alert('Gagal terhubung ke server.');
            $('#saveButton').prop('disabled', false).text('Simpan Hasil Prediksi');
          }
        });
      });

    });
  </script>
@endsection
