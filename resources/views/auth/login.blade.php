<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login - {{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background-color: white;
            }
            .login-page-wrapper {
                min-height: 100vh;
                display: flex;
                width: 100%;
                position: relative;
            }
            .illustration-side {
                width: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                padding-top: 120px;
                padding-left: 2rem;
                padding-right: 2rem;
            }
            .illustration-side img {
                max-width: 100%;
                max-height: 80vh;
                height: auto;
                object-fit: contain;
            }
            .form-side {
                width: 50%;
                display: flex;
                justify-content: flex-start;
                align-items: flex-start;
                padding-left: 5%;
                padding-top: 15vh;
                padding-bottom: 5rem;
            }
            .login-box {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                width: 100%;
                max-width: 450px;
                padding: 40px;
                text-align: center;
            }
            .logo-container-top-left {
                position: absolute;
                top: 2rem;
                left: 2rem;
                display: flex;
                align-items: center;
                gap: 1.5rem;
                z-index: 10;
            }
            .logo-container-top-left img {
                max-height: 60px;
                width: auto;
            }
             .logo-divider {
                height: 50px;
                width: 1px;
                background-color: #d1d5db;
            }
            .info-box {
                background-color: #e3f2fd;
                border-left: 5px solid #2196F3;
                padding: 1rem;
                margin-top: 2rem;
                text-align: left;
                font-size: 0.875rem;
                color: #424242;
                line-height: 1.6;
            }
            .info-box strong {
                color: #0d47a1;
                display: block;
                margin-bottom: 0.5rem;
            }
            .captcha-img {
                border-radius: 5px;
                border: 1px solid #d1d5db;
            }
            .copyright-text {
                position: absolute;
                bottom: 0.5rem;
                right: 2rem;
                text-align: right;
                font-size: 0.8rem;
                color: #888;
                z-index: 10;
            }
            .privacy-link {
                color: #2196F3;
                text-decoration: none;
            }
            .privacy-link:hover {
                text-decoration: underline;
            }

            /* === CSS BARU UNTUK RESPONSIVE === */
            @media (max-width: 1024px) { /* Untuk tablet dan di bawahnya */
                .illustration-side {
                    display: none; /* Sembunyikan kolom ilustrasi */
                }
                .form-side {
                    width: 100%; /* Jadikan kolom form lebar penuh */
                    justify-content: center; /* Tengahkan form */
                    align-items: center;
                    padding: 2rem;
                    padding-top: 10vh;
                }
                .logo-container-top-left {
                    /* Pusatkan logo di atas form untuk tampilan mobile */
                    left: 50%;
                    transform: translateX(-50%);
                }
                .copyright-text {
                    /* Pusatkan copyright di bawah untuk tampilan mobile */
                    width: 100%;
                    left: 0;
                    right: 0;
                    text-align: center;
                    padding: 0 1rem;
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="login-page-wrapper">

            <!-- Logo di pojok kiri atas -->
            <div class="logo-container-top-left">
                <img src="{{ asset('images/Lambang_polri.png') }}" alt="Logo Polri">
                <div class="logo-divider"></div>
                <img src="{{ asset('images/logo_bagren.png') }}" alt="Logo Keuangan">
            </div>

            <!-- Kolom Kiri (Untuk Ilustrasi) -->
            <div class="illustration-side">
                <img src="{{ asset('images/login_illustration.png') }}" alt="Login Illustration">
            </div>

            <!-- Kolom Kanan (Untuk Form) -->
            <div class="form-side">
                <div class="login-box">
                    <h2 class="text-2xl font-bold mb-6">Login</h2>
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div>
                            <input id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="email" name="email" :value="old('email')" required autofocus placeholder="ID Pengguna (Email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <input id="password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="password" name="password" required autocomplete="current-password" placeholder="Kata Sandi" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center space-x-2">
                                <input id="captcha" class="block w-full border-gray-300 rounded-md shadow-sm" type="text" name="captcha" required placeholder="Kode Captcha" />
                                <span class="captcha-img">{!! captcha_img('flat') !!}</span>
                            </div>
                             <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-700 active:bg-blue-900">
                                {{ __('Masuk') }}
                            </button>
                        </div>
                    </form>

                    <div class="info-box">
                        <strong>INFO PENTING</strong>
                        <p>• Lindungi akun Anda dengan tidak memberikan ID pengguna dan kata sandi Anda pada siapapun.</p>
                        <p>• Segala risiko akibat penyalahgunaan ID pengguna menjadi tanggung jawab pengguna sepenuhnya.</p>
                        <p>• Kami menjamin kerahasiaan data setiap pengguna sebagai bentuk penghargaan kami terhadap privasi.</p>
                        <p>• Seluruh fitur dan layanan dapat diakses secara gratis tanpa tambahan biaya apapun.</p>
                    </div>
                </div>
            </div>

            <!-- Teks Copyright -->
            <div class="copyright-text">
                <a href="#" class="privacy-link">Kebijakan Privasi</a> © {{ date('Y') }} Bagian Perencanaan Polres Garut. Seluruh hak cipta dilindungi.
            </div>

        </div>
    </body>
</html>

