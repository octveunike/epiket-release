@extends('layouts.auth')

@section('title', 'Login - SMAN 1 Cibinong')

@push('styles')
<style>
    .confirm-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: 0.2s ease;
        z-index: 9999;
    }

    .confirm-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .confirm-box {
        background: white;
        padding: 32px;
        border-radius: 10px;
        width: 360px;
        max-width: 90%;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        font-family: 'Poppins', sans-serif;
    }

    .confirm-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 3px solid #f59e0b;
        color: #f59e0b;
        font-size: 28px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .confirm-box h3 {
        margin: 0 0 8px;
        color: #1f2937;
    }

    .confirm-box p {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    .confirm-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-modal {
        padding: 10px 22px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        transition: all 0.2s ease;
    }

    .btn-modal-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-modal-secondary:hover {
        background: #d1d5db;
    }
</style>
@endpush

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

                            <a href="#" class="forgot-password" id="forgotPasswordLink">
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

{{-- Forgot Password Modal --}}
<div class="confirm-overlay" id="forgotPasswordModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Lupa Password?</h3>
        <p>Silakan hubungi admin untuk mengubah password anda.</p>
        <div class="confirm-actions">
            <button type="button" class="btn-modal btn-modal-secondary" onclick="closeForgotPasswordModal()">Tutup</button>
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

document.getElementById("forgotPasswordLink").addEventListener("click", function (e) {
    e.preventDefault();
    document.getElementById("forgotPasswordModal").classList.add("show");
});

function closeForgotPasswordModal() {
    document.getElementById("forgotPasswordModal").classList.remove("show");
}

document.getElementById("forgotPasswordModal").addEventListener("click", function (e) {
    if (e.target === this) closeForgotPasswordModal();
});
</script>

@endsection
