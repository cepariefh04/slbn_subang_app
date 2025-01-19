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
                <td class="aset-id" data-aset-id="{{ $item->id }}">{{ $item->id }}</td>
                <td>{{ $item->nama }}</td>
                <td class="prediction-result" data-aset="{{ $item->nama }}">Menunggu...</td>
                <td class="prediction-status" data-aset="{{ $item->nama }}">-</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{-- <nav aria-label="Page navigation example">
          <ul class="pagination round-pagination justify-content-center">
            <!-- Tombol Previous -->
            @if ($assets->onFirstPage())
              <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
              </li>
            @else
              <li class="page-item">
                <a class="page-link" href="{{ $assets->previousPageUrl() }}">Previous</a>
              </li>
            @endif

            <!-- Nomor Halaman -->
            @for ($page = 1; $page <= $assets->lastPage(); $page++)
              @if ($page == $assets->currentPage())
                <li class="page-item active">
                  <a class="page-link" href="#">{{ $page }}</a>
                </li>
              @else
                <li class="page-item">
                  <a class="page-link" href="{{ $assets->url($page) }}">{{ $page }}</a>
                </li>
              @endif
            @endfor

            <!-- Tombol Next -->
            @if ($assets->hasMorePages())
              <li class="page-item">
                <a class="page-link" href="{{ $assets->nextPageUrl() }}">Next</a>
              </li>
            @else
              <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Next</a>
              </li>
            @endif
          </ul>
        </nav> --}}

      </div>
    </div>
  </div>
  <div class="overlay nav-toggle-icon"></div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#predictButton').on('click', function() {
        // Ubah status tombol saat proses berlangsung
        $(this).prop('disabled', true).text('Memproses...');
        // Lakukan request AJAX
        $.ajax({
          url: "{{ route('sarpras.proses-prediksi') }}",
          method: "GET",
          success: function(response) {
            if (response.success) {
              const predictions = response.predictions;

              // Perbarui tabel dengan hasil prediksi
              for (const [asetName, predictedValue] of Object.entries(predictions)) {
                $(`.prediction-result[data-aset="${asetName}"]`).text(predictedValue);
                $(`.prediction-status[data-aset="${asetName}"]`).text('Berhasil');
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
            $('#predictButton').prop('disabled', false).text('Prediksi Ulang');
            $('#saveButton').prop('disabled', false);
          }
        });
      });

      $('#saveButton').on('click', function() {
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
            tahun: "2024-2025"
          },
          success: function(response) {
            if (response.success) {
              alert(response.message);
              $('#saveButton').prop('disabled', true); // Nonaktifkan tombol setelah disimpan
            } else {
              alert("Gagal menyimpan hasil prediksi.");
            }
          },
          error: function() {
            alert("Terjadi kesalahan saat menyimpan data.");
          },
        });
      });
    });
  </script>
@endsection
