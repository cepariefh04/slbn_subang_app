@extends('layouts.auth')
@section('container')
  <main class="authentication-content">
    <div class="container-fluid">
      <div class="authentication-card">
        <div class="card shadow rounded-0 overflow-hidden">
          <div class="row g-0">
            <div class="col-lg-6 bg-login d-flex align-items-center justify-content-center">
              <img src="assets/images/error/login-img.jpg" class="img-fluid" alt="">
            </div>
            <div class="col-lg-6">
              <div class="card-body p-4 p-sm-5">
                <h5 class="card-title">Register</h5>
                <form class="form-body" method="POST" action="{{ route('register.submit') }}">
                  @csrf
                  {{-- <div class="d-grid">
                    <a class="btn btn-white radius-30" href="javascript:;"><span
                        class="d-flex justify-content-center align-items-center">
                        <img class="me-2" src="assets/images/icons/search.svg" width="16" alt="">
                        <span>Sign up with Google</span>
                      </span>
                    </a>
                  </div>
                  <div class="login-separater text-center mb-4"> <span>OR SIGN UP WITH EMAIL</span>
                    <hr>
                  </div> --}}
                  <div class="row g-3">
                    <div class="col-12 ">
                      <label for="inputName" class="form-label">Full Name</label>
                      <div class="ms-auto position-relative">
                        <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                            class="bi bi-person-circle"></i></div>
                        <input type="text" class="form-control radius-30 ps-5" id="inputName" name="name"
                          placeholder="Enter Name">
                      </div>
                    </div>
                    <div class="col-12">
                      <label for="inputEmailAddress" class="form-label">Email Address</label>
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
                    {{-- <div class="col-12">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked">
                        <label class="form-check-label" for="flexSwitchCheckChecked">I Agree to the Trems &
                          Conditions</label>
                      </div>
                    </div> --}}
                    <div class="col-12">
                      <div class="d-grid">
                        <button type="submit" class="btn btn-primary radius-30">Register</button>
                      </div>
                    </div>
                    <div class="col-12">
                      <p class="mb-0">Already have an account? <a href="authentication-signin.html">Login here</a></p>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection