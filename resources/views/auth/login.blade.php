@extends('layouts.auth')

@section('title', 'Login - SMAN 1 Cibinong')

@section('content')
<div class="login-wrapper">
    <div class="login-container">
        <div class="login-left">
            <div class="logo-container">
                <img src="{{ asset('images/logo-sman1-cibinong.png') }}" alt="Logo SMAN 1 Cibinong">
            </div>
            <div class="school-name">
                <h1>SMAN 1 CIBINONG</h1>
                <p>Sistem Informasi E-Piket</p>
            </div>
        </div>

        <div class="login-right">
            <div class="login-box">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk ke akun Anda</p>
            </div>

            @if ($errors->has('login'))
                <div class="alert alert-danger">
                    {{ $errors->first('login') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="username"
                                name="username" 
                                class="form-control" 
                                placeholder="Masukkan username Anda"
                                value="{{ old('username') }}"
                                required 
                                autofocus>
                        </div>
                        @error('username')
                            <small style="color: #c62828;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="label-row">
                            <label for="password">Password</label>

                            <a href="{{ route('password.request') }}" class="forgot-password">
                                Lupa Password?
                            </a>
                        </div>

                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password"
                                name="password" 
                                class="form-control password-input"
                                placeholder="Masukkan password Anda"
                                required>

                            <span class="toggle-password" id="togglePassword">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>

                        @error('password')
                            <small style="color: #c62828;">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("togglePassword").addEventListener("click", function () {
    const password = document.getElementById("password");
    const icon = this.querySelector("i");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        password.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
});
</script>

@endsection