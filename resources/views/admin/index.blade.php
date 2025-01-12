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
                <form class="form-body" method="POST" action="{{ route('admin.tambahPengguna') }}">
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
                    <img src="assets/images/avatars/avatar-1.png" class="rounded-circle" width="44" height="44"
                      alt="">
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
                      <form action="{{ route('admin.hapusPengguna', $item->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this item?');">
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
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="overlay nav-toggle-icon"></div>
  <!--end footer-->
@endsection
