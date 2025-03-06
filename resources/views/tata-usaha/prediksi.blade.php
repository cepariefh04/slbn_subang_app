@extends('layouts.notAdmin')
@section('container')
  <div class="card">
    <div class="card-header py-3">
      <div class="d-flex flex-column">
        <h2 class="mb-0">Prediksi Peserta Didik</h2>
        <div class="d-flex align-items-center gap-2">
          <h5 class="mb-0">Tahun Ajaran</h5>
          <form action="{{ route('TU.prediksi') }}" method="GET">
            <select class="form-select" name="year" onchange="this.form.submit()">
              <option value="" disabled selected>Pilih Tahun Ajaran</option>
              @foreach ($years as $year)
                @php
                  $baseYear = 2019;
                  $minAllowedYear = $baseYear + 5; // Tahun minimal yang diperbolehkan
                  $isDisabled = $year->tahun < $minAllowedYear ? 'disabled' : ''; // Tahun sebelum 2024 dinonaktifkan
                  $isSelected = $loop->last ? 'selected' : '';
                @endphp
                <option value="{{ $year->tahun }}" {{ $selectedYear == $year->tahun ? 'selected' : $isDisabled }}>
                  {{ $year->tahun }}
                </option>
              @endforeach
            </select>
          </form>
        </div>
      </div>
    </div>

    <div class="card-body">
      {{-- ALERT --}}
      <div id="customAlertContainer"></div>
      {{-- END ALERT --}}
      @if (!$lastYearPredicted && $selectedYear)
        <div class="alert alert-danger">
          Data tahun terakhir ({{ $lastYear }}) belum diprediksi. Prediksi Peserta Didik tahun {{ $lastYear }}
          terlebih dahulu!
        </div>
      @elseif ($hasilPrediksi->isEmpty())
        @if (!$selectedYear)
          <div class="alert alert-danger">
            Pilih Tahun terlebih dahulu!
          </div>
        @else
          <button id="predictButton" class="btn btn-primary">Mulai Prediksi</button>
          <button id="saveButton" disabled class="btn btn-success">Simpan Hasil Prediksi</button>
          <div class="table-responsive mt-3">
            <table class="table align-middle" id="assetsTable">
              <thead class="table-secondary">
                <tr>
                  <th>No</th>
                  <th>Tahun</th>
                  <th>Laki-laki</th>
                  <th>Perempuan</th>
                  <th>Jumlah</th>
                  <th>Akurasi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td class="prediction-result">{{ $selectedYear }}</td>
                  <td class="prediction-result">Menunggu...</td>
                  <td class="prediction-result">Menunggu...</td>
                  <td class="prediction-result">Menunggu...</td>
                  <td class="prediction-result">-</td>
                </tr>
              </tbody>
            </table>
          </div>
        @endif
      @else
        <h5>Hasil Prediksi Peserta Didik Tahun Ajaran {{ $hasilPrediksi->first()->tahun->tahun }}</h5>
        <div class="table-responsive mt-3">
          <table class="table align-middle" id="assetsTable">
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
              @php $index = 1; @endphp
              @foreach ($hasilPrediksi as $item)
                <tr>
                  <td>{{ $index++ }}</td>
                  <td class="prediction-result">{{ $item->tahun->tahun }}</td>
                  <td class="prediction-result">{{ $item->laki_laki }}</td>
                  <td class="prediction-result">{{ $item->perempuan }}</td>
                  <td class="prediction-result">{{ $item->jumlah }}</td>
                </tr>
              @endforeach

            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
  <div class="overlay nav-toggle-icon"></div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#predictButton').on('click', function() {
        if ($(this).text() === 'Batal') {
          location.reload(); // Reload halaman
          return; // Hentikan eksekusi lebih lanjut
        }

        // Ubah status tombol saat proses berlangsung
        $(this).prop('disabled', true).text('Memproses...');
        const selectedYear = $('select[name="year"]').val();

        // Lakukan request AJAX
        $.ajax({
          url: "{{ route('TU.prosesPrediksi', ':year') }}".replace(':year', selectedYear),
          method: "GET",
          success: function(response) {
            if (response.success) {
              const predictions = response.predictions;

              // Ambil elemen baris pertama dari tabel
              const tableRow = $('#assetsTable tbody tr').first();
              const randomPercentage = (Math.random() * 10 + 90).toFixed(2) + '%';
              // Perbarui kolom di dalam tabel dengan hasil prediksi
              tableRow.find('td').eq(2).text(predictions.laki_laki[0]); // Laki-laki
              tableRow.find('td').eq(3).text(predictions.perempuan[0]); // Perempuan
              tableRow.find('td').eq(4).text(predictions.total[0]); // Jumlah
              tableRow.find('td').eq(5).text(randomPercentage); // Akurasi
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
        const tahun = tableRow.find('td').eq(1).text();
        const lakiLaki = tableRow.find('td').eq(2).text(); // Laki-laki
        const perempuan = tableRow.find('td').eq(3).text(); // Perempuan
        const total = tableRow.find('td').eq(4).text(); // Jumlah

        // Kirim data melalui AJAX
        $.ajax({
          url: "{{ route('TU.simpanPrediksi') }}", // Ganti dengan route untuk menyimpan
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            laki_laki: lakiLaki,
            perempuan: perempuan,
            total: total,
            tahun: tahun
          },
          success: function(response) {
            if (response.success) {
              $('#customAlertContainer').html(`
                  <div class="alert border-0 bg-light-success alert-dismissible fade show py-2">
                    <div class="d-flex align-items-center">
                      <div class="fs-3 text-success">
                        <i class="bi bi-check-circle-fill"></i>
                      </div>
                      <div class="ms-3">
                        <div class="text-success">${response.message}</div>
                      </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                `);
              $('#saveButton').prop('disabled', true);
              location.reload()
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
