@extends('layouts.auth')
@section('container')
  <main class="authentication-content">
    <div class="container-fluid">
      <div class="authentication-card">
        <div class="card shadow rounded-0 overflow-hidden">
          <div class="row align-items-center g-0">
            <div class="col-lg-6 d-flex align-items-center justify-content-center">
              <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid py-4" width="400" alt="">
            </div>
            <div class="col-lg-6">
              <div class="card-body p-4 p-sm-5">
                <h4 class="text-center" style="color: #007A3D">Sistem Prediksi Kebutuhan Aset SLB Negeri Subang</h4>
                <h5 class="card-title">Login</h5>
                <form class="form-body" action="{{ route('login.submit') }}" method="post">
                  @csrf
                  {{-- <div class="d-grid">
                    <a class="btn btn-white radius-30" href="javascript:;"><span class="d-flex justify-content-center align-items-center">
                      <img class="me-2" src="assets/images/icons/search.svg" width="16" alt="">
                      <span>Sign in with Google</span>
                    </span>
                  </a>
                </div>
                <div class="login-separater text-center mb-4"> <span>OR SIGN IN WITH EMAIL</span>
                    <hr>
                  </div> --}}
                  <div class="row g-3">
                    <div class="col-12">
                      <label for="inputEmailAddress" class="form-label">Email</label>
                      <div class="ms-auto position-relative">
                        <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                            class="bi bi-envelope-fill"></i></div>
                        <input type="email" class="form-control radius-30 ps-5" id="inputEmailAddress" name="email"
                          placeholder="Email Address">
                      </div>
                    </div>
                    <div class="col-12">
                      <label for="inputChoosePassword" class="form-label">Enter Password</label>
                      <div class="ms-auto position-relative">
                        <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                            class="bi bi-lock-fill"></i></div>
                        <input type="password" class="form-control radius-30 ps-5" id="inputChoosePassword"
                          name="password" placeholder="Enter Password">
                      </div>
                    </div>
                    {{-- <div class="col-6">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked="">
                        <label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
                      </div>
                    </div>
                    <div class="col-6 text-end">	<a href="authentication-forgot-password.html">Forgot Password ?</a>
                  </div> --}}
                    <div class="col-12">
                      <div class="d-grid">
                        <button type="submit" class="btn btn-primary radius-30">Login</button>
                      </div>
                    </div>
                  </div>
                </form>
                @if (session('gagal'))
                  <p class="text-danger">{{ session('gagal') }}</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
