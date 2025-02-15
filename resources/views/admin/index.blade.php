@extends('layouts.admin')
@section('adminContainer')
  <div class="card">
    <div class="card-body">
      <div class="d-flex align-items-center">
        <h5 class="mb-0">Daftar Pengguna</h5>
        <div class="ms-auto position-relative">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#exampleVerticallycenteredModal"><i class="bi bi-plus"></i> Tambah Pengguna</button>
          <div class="modal fade" id="exampleVerticallycenteredModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Tambah Pengguna</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form-body" method="POST" action="{{ route('admin.tambahPengguna') }}"
                  enctype="multipart/form-data">
                  <div class="modal-body">
                    @csrf
                    <div class="row g-3">
                      <div class="col-12 ">
                        <label for="inputName" class="form-label">Nama</label>
                        <div class="ms-auto position-relative">
                          <input type="text" class="form-control" id="inputName" name="name"
                            placeholder="Masukkan Nama">
                        </div>
                      </div>
                      <div class="col-12">
                        <label for="inputEmailAddress" class="form-label">Email</label>
                        <div class="ms-auto position-relative">
                          <input type="email" class="form-control" id="inputEmailAddress" name="email"
                            placeholder="Masukkan Email yang valid">
                        </div>
                      </div>
                      <div class="col-12">
                        <label for="inputPassword" class="form-label">Password</label>
                        <div class="ms-auto position-relative">
                          <input type="password" class="form-control" id="inputPassword" name="password"
                            placeholder="Masukkan Password">
                        </div>
                      </div>
                      <div class="col-12">
                        <label for="inputFoto" class="form-label">Foto</label>
                        <div class="ms-auto position-relative">
                          <input type="file" class="form-control" id="inputFoto" name="photo" accept="image/*"
                            onchange="previewPhoto(event)">
                        </div>
                        <div class="mt-3">
                          <img id="photoPreview" src="#" alt="Preview Foto" class="img-thumbnail d-none"
                            style="max-width: 150px;">
                        </div>
                      </div>
                      <div class="col-12">
                        <label for="selectLevel" class="form-label">Level Pengguna</label>
                        <div class="ms-auto position-relative">
                          <select class="form-select mb-3" id="selectLevel" name="level">
                            <option selected disabled>Pilih level pengguna</option>
                            <option value="Admin">Admin</option>
                            <option value="SarPras">SarPras</option>
                            <option value="TU">TU</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
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
              <th>#</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Terakhir Login</th>
              <th>Level</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @php
              $index = 1;
            @endphp
            @foreach ($users as $item)
              <tr>
                <td>{{ $index++ }}</td>
                <td>
                  <div class="d-flex align-items-center gap-3 cursor-pointer">
                    <img src="{{ $item->photo ? asset($storagePath . $item->photo) : 'assets/images/avatars/user.png' }}"
                      class="rounded-circle" width="44" height="44" alt="">
                    <div class="">
                      <p class="mb-0 text-capitalize">{{ $item->name }}</p>
                    </div>
                  </div>
                </td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->last_login_at ?? 'belum login' }}</td>
                <td>{{ $item->level }}</td>
                <td>
                  <div class="table-actions d-flex align-items-center gap-3 fs-6">
                    @if ($item->level !== 'Admin')
                      <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                        data-bs-target="#editUserModal{{ $item->id }}">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                      <form action="{{ route('admin.hapusPengguna', $item->id) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus Data Pengguna tersebut?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" data-bs-toggle="tooltip"
                          data-bs-placement="bottom" title="Delete">
                          <i class="bi bi-trash-fill mx-0"></i>
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>

              <!-- Modal Edit -->
              <div class="modal fade" id="editUserModal{{ $item->id }}" tabindex="-1"
                aria-labelledby="editUserModalLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="{{ route('admin.updatePengguna', $item->id) }}" method="POST"
                      enctype="multipart/form-data">
                      @csrf
                      @method('PUT')
                      <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel{{ $item->id }}">Edit Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label for="editName{{ $item->id }}" class="form-label">Nama</label>
                          <input type="text" class="form-control" id="editName{{ $item->id }}" name="name"
                            value="{{ $item->name }}" required>
                        </div>
                        <div class="mb-3">
                          <label for="editEmail{{ $item->id }}" class="form-label">Email</label>
                          <input type="email" class="form-control" id="editEmail{{ $item->id }}" name="email"
                            value="{{ $item->email }}" required>
                        </div>
                        <div class="mb-3">
                          <label for="editPassword{{ $item->id }}" class="form-label">Password</label>
                          <input type="password" class="form-control" id="editPassword{{ $item->id }}"
                            name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                        <div class="mb-3">
                          <label for="editPhoto{{ $item->id }}" class="form-label">Photo</label>
                          <input type="file" class="form-control" id="editPhoto{{ $item->id }}" name="photo"
                            onchange="previewPhoto(event)">
                        </div>
                        <div class="mt-3">
                          <img id="photoPreview" src="{{ asset($storagePath . $item->photo) }}" alt="Preview Foto"
                            class="rounded" width="100" height="100">
                        </div>
                        <div class="mb-3">
                          <label for="editLevel{{ $item->id }}" class="form-label">Level</label>
                          <select class="form-select" id="editLevel{{ $item->id }}" name="level" required>
                            <option value="Admin" {{ $item->level === 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="SarPras" {{ $item->level === 'SarPras' ? 'selected' : '' }}>SarPras</option>
                            <option value="TU" {{ $item->level === 'TU' ? 'selected' : '' }}>TU</option>
                          </select>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
    function previewPhoto(event) {
      const file = event.target.files[0];
      const preview = document.getElementById('photoPreview');

      if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.remove('d-none');
        };

        reader.readAsDataURL(file);
      } else {
        preview.src = "#";
        preview.classList.add('d-none');
      }
    }
  </script>
  <!--end footer-->
@endsection
