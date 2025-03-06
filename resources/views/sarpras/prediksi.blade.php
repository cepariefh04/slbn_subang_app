@extends('layouts.notAdmin')
@section('container')
  <div class="card">
    <div class="card-header py-3">
      <div class="d-flex flex-column">
        <h2 class="mb-0">Prediksi Aset</h2>
        <div class="d-flex align-items-center gap-2">
          <h5 class="mb-0">Tahun Ajaran</h5>
          <form action="{{ route('sarpras.prediksi') }}" method="GET">
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
          Data tahun terakhir ({{ $lastYear }}) belum diprediksi dan belum diajukan. Prediksi kemudian lakukan
          pengajuan data tahun {{ $lastYear }} terlebih dahulu!
        </div>
      @elseif ($riwayats->isEmpty())
        @if (!$selectedYear)
          <div class="alert alert-danger">
            Pilih Tahu terlebih dahulu!
          </div>
        @elseif (!$hasilPrediksiPeserta)
          <div class="alert alert-danger">
            Tata Usaha belum melakukan prediksi peserta didik untuk tahun {{ $selectedYear }}, Hubungi pihak terkait untuk
            melakukan prediksi peserta didik.
          </div>
        @else
          <button id="predictButton" class="btn btn-primary">Mulai Prediksi</button>
          <button id="saveButton" disabled class="btn btn-success">Simpan Hasil Prediksi</button>
          <div class="table-responsive mt-3">
            <table class="table align-middle" id="assetsTable">
              <thead class="table-secondary">
                <tr>
                  <th>No</th>
                  <th>Nama Aset</th>
                  <th>Hasil Prediksi</th>
                  <th>Akurasi</th>
                </tr>
              </thead>
              <tbody>
                @php $index = 1; @endphp
                @foreach ($assets as $item)
                  <tr>
                    <td class="aset-id" data-aset-id="{{ $item->id }}">{{ $index++ }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="prediction-result" data-aset="{{ $item->nama }}">Menunggu...</td>
                    <td class="prediction-status" data-aset="{{ $item->nama }}">-</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      @else
        <div class="d-flex align-items-center justify-content-between gap-2">
          <h5>Hasil Prediksi Tahun Ajaran {{ $riwayats->first()->tahun->tahun }}</h5>
          <a href="/dashboard/sarpras/pengajuan" class="btn btn-outline-primary">Ajukan Pengajuan</a>
        </div>
        <div class="table-responsive mt-3">
          <table class="table align-middle" id="assetsTable">
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
              @php $index = 1; @endphp
              @foreach ($riwayats as $item)
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
        console.log(selectedYear);

        if (selectedYear === null) {
          $('#customAlertContainer').html(`
              <div class="alert border-0 bg-light-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                  <div class="fs-3 text-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                  </div>
                  <div class="ms-3">
                    <div class="text-danger">Pilih Tahun terlebih dahulu</div>
                  </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            `);
        }
        // Lakukan request AJAX
        $.ajax({
          url: "{{ route('sarpras.proses-prediksi', ':year') }}".replace(':year', selectedYear),
          method: "GET",
          success: function(response) {
            if (response.success) {
              const predictions = response.predictions;
              const accurates = response.akurasi;

              // Perbarui tabel dengan hasil prediksi
              for (const [asetName, predictedValue] of Object.entries(predictions)) {
                $(`.prediction-result[data-aset="${asetName}"]`).text(predictedValue);
              }

              for (const [asetName, accuration] of Object.entries(accurates)) {
                $(`.prediction-status[data-aset="${asetName}"]`).text(accuration);
              }
            } else {
              alert("Terjadi kesalahan dalam proses prediksi!");
            }
          },
          error: function() {
            alert("Gagal terhubung ke server.");
          },
          complete: function() {
            // Aktifkan kembali tombol setelah proses selesai
            $('#predictButton').prop('disabled', false).text('Batal');
            $('#saveButton').prop('disabled', false);
          }
        });
      });

      $('#saveButton').on('click', function() {
        const selectedYear = $('select[name="year"]').val();
        // Kumpulkan data prediksi dari tabel
        const predictions = [];
        $('#assetsTable tr').each(function() {
          const asetId = $(this).find('.aset-id').data('aset-id');
          const predictedValue = $(this).find('.prediction-result').text();

          if (asetId && predictedValue) {
            predictions.push({
              aset_id: asetId,
              predicted_value: parseInt(predictedValue),
            });
          }
        });

        // Kirim data ke server
        $.ajax({
          url: "{{ route('sarpras.storePrediksi') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            predictions: predictions,
            tahun: selectedYear
          },
          success: function(response) {
            // Clear previous alert
            $('#customAlertContainer').empty();

            if (response.success) {
              // Tambahkan alert sukses ke container
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
            // Tambahkan alert error ke container
            $('#customAlertContainer').html(`
              <div class="alert border-0 bg-light-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                  <div class="fs-3 text-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                  </div>
                  <div class="ms-3">
                    <div class="text-danger">Terjadi kesalahan saat menyimpan data.</div>
                  </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            `);
          },
        });
      });
    });
  </script>
@endsection
