@extends('layouts.notAdmin')
@section('container')
  @if (!$riwayat)
    <x-alert type="danger" :message="$message" />
  @else
    <div id="customAlertContainer"></div>
    <button id="ajukanButton" class="btn btn-primary">Ajukan</button>
    <button id="showButton" class="btn btn-success" style="display: none;" data-bs-toggle="modal"
      data-bs-target="#hasilDataAset">Lihat Data Aset</button>
    <div class="row mt-4">
      <div class="col-12">
        <p>
          Keterangan : <b>JA</b> = Jumlah Aset, <b>JL</b> = Jumlah Layak, <b>JTL</b> = Jumlah Tidak Layak.
        </p>
      </div>
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
                      <td>{{ $item->jumlah }}</td>
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
      <div class="col-5">
        <div class="card">
          <div class="card-header py-3">
            <div class="d-flex flex-column">
              <h5 class="mb-0">Pengajuan Kebutuhan Aset Tahun {{ $riwayat->first()->tahun->tahun }}</h2>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive mt-3">
              <table class="table align-middle" id="assetsTable">
                <thead class="table-secondary">
                  <tr>
                    <th>No</th>
                    <th>Nama Aset</th>
                    <th>Jumlah Pengajuan</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($riwayat as $item)
                    <tr>
                      <td>{{ $item->id }}</td>
                      <td>{{ $item->aset->nama }}</td>
                      <td class="pengajuan-result" data-aset="{{ $item->id - 1 }}">Menunggu...</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="hasilDataAset" tabindex="-1" aria-labelledby="hasilDataAsetLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Hasil Akhir Kebutuhan Aset Tahun {{ $riwayat->first()->tahun->tahun }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="card">
              <div class="card-body">
                <p class="mb-0">Dibawah adalah tabel yang berisi data Kebutuhan Aset yang telah melalui proses Prediksi
                  dan Pengajuan untuk tahun {{ $riwayat->first()->tahun->tahun }}.</p>
                <p class="mb-0">Simpan data pada tabel ini kedalam database untuk digunakan sebagai data acuan untuk
                  proses prediksi ditahun berikutnya.</p>
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
                      @foreach ($riwayat as $item)
                        <tr>
                          <td class="aset-id" data-aset-id="{{ $item->id }}">{{ $item->id }}</td>
                          <td class="tahun-id" data-tahun-id="{{ $item->tahun->id }}">{{ $item->tahun->tahun }}</td>
                          <td>{{ $item->aset->nama }}</td>
                          <td class="final-jumlah-aset" data-aset="{{ $item->id - 1 }}"></td>
                          <td class="final-jumlah-layak" data-aset="{{ $item->id - 1 }}"></td>
                          <td>{{ $item->jumlah_tidak_layak }}</td>
                          <td class="pengajuan-result d-none" data-aset="{{ $item->id - 1 }}"></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveAssetButton">Simpan Aset</button>
          </div>
        </div>
      </div>
    </div>
    <div class="overlay nav-toggle-icon"></div>
    <!--end footer-->
  @endif
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#showButton').hide();

      $('#ajukanButton').on('click', function() {
        if ($(this).text() === 'Batal') {
          location.reload(); // Reload halaman
          return;
        }

        // Ubah status tombol saat proses berlangsung
        $(this).prop('disabled', true).text('Memproses...');

        // Lakukan request AJAX
        $.ajax({
          url: "{{ route('sarpras.proses-pengajuan') }}",
          method: "GET",
          success: function(response) {
            if (response.success) {
              const pengajuans = response.pengajuan;
              const finalResults = response.finalAsets

              // Perbarui tabel dengan hasil prediksi
              for (const [index, pengajuanValue] of Object.entries(pengajuans)) {
                $(`.pengajuan-result[data-aset="${index}"]`).html(pengajuanValue.jumlah_pengajuan === 0 ?
                  `<i class="text-success">Tidak Perlu Pengajuan</i>` : pengajuanValue.jumlah_pengajuan);
              }

              for (const [index, finalResultValues] of Object.entries(finalResults)) {
                $(`.final-jumlah-aset[data-aset="${index}"]`).text(finalResultValues.jumlah_aset);
                $(`.final-jumlah-layak[data-aset="${index}"]`).text(finalResultValues.jumlah_layak);
              }

              // **Jika sukses, sembunyikan #ajukanButton dan tampilkan #showButton**
              $('#ajukanButton').hide();
              $('#showButton').show();
            } else {
              alert("Terjadi kesalahan dalam proses prediksi!");
              $('#ajukanButton').prop('disabled', false).text('Ajukan');
            }
          },
          error: function() {
            alert("Gagal terhubung ke server.");
            $('#ajukanButton').prop('disabled', false).text('Ajukan');
          }
        });
      });

      $('#saveAssetButton').on('click', function() {
        const selectedYear = $('select[name="year"]').val();
        // Kumpulkan data prediksi dari tabel
        const results = [];


        $('#assetsTable tr').each(function() {
          const asetId = $(this).find('.aset-id').data('aset-id');
          const tahunId = $(this).find('.tahun-id').data('tahun-id');
          const finalJumlahAset = $(this).find('.final-jumlah-aset').text();
          const finalJumlahLayak = $(this).find('.final-jumlah-layak').text();
          const pengajuan = $(this).find('.pengajuan-result').text();
          const jumlahPengajuan = pengajuan === "Tidak Perlu Pengajuan" ? "0" : pengajuan

          if (asetId && finalJumlahAset && finalJumlahLayak && tahunId && jumlahPengajuan) {
            results.push({
              aset_id: asetId,
              tahun_id: tahunId,
              jumlah_aset: parseInt(finalJumlahAset),
              jumlah_layak: parseInt(finalJumlahLayak),
              jumlah_pengajuan: parseInt(jumlahPengajuan),
            });
          }
        });

        // Kirim data ke server
        $.ajax({
          url: "{{ route('sarpras.storeFinalResultAsset') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            results: results,
          },
          success: function(response) {
            // Clear previous alert
            $('#customAlertContainer').empty();

            if (response.success) {
              $('#hasilDataAset').modal('hide');

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
              $('#saveAssetButton').prop('disabled', true); // Nonaktifkan tombol setelah disimpan
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
