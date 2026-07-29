<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – SIPINJAM | Sistem Informasi Peminjaman Ruangan dan Barang Dinas Kesehatan Kota Batam</title>
    <meta name="description" content="Sistem Informasi Peminjaman Ruangan dan Barang Dinas Kesehatan Kota Batam">
    <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-dark:  #006633;
            --green-main:  #00A538;
            --green-light: #00C84A;
            --green-pale:  #E8F8EE;
            --red-accent:  #CC0000;
            --text-dark:   #1a2e1a;
            --text-muted:  #5a7a5a;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f7f2;
        }

        /* ── LEFT PANEL ─────────────────────────────── */
        .left-panel {
            flex: 1;
            background: linear-gradient(160deg, var(--green-dark) 0%, var(--green-main) 55%, #00c853 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
        }

        /* decorative circles */
        .left-panel::before {
            content: '';
            position: absolute;
            width: 450px; height: 450px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.08);
            top: -120px; left: -120px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.08);
            bottom: -100px; right: -80px;
        }

        .circle-deco {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
        }
        .circle-deco.c1 { width:200px; height:200px; top:10%; right:10%; }
        .circle-deco.c2 { width:120px; height:120px; bottom:20%; left:8%; }
        .circle-deco.c3 { width: 80px; height: 80px;  top:55%; right:5%; }

        .left-content { position: relative; z-index: 1; text-align: center; max-width: 400px; }

        .logo-dinkes {
            background: rgba(255,255,255,.95);
            border-radius: 16px;
            padding: 16px 28px;
            display: inline-block;
            margin-bottom: 36px;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }
        .logo-dinkes img { height: 80px; object-fit: contain; }

        .left-content h1 {
            color: #fff;
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 12px;
            text-shadow: 0 2px 12px rgba(0,0,0,.2);
        }
        .left-content h1 span { color: #a8ffc4; }

        .left-content p {
            color: rgba(255,255,255,.8);
            font-size: .88rem;
            line-height: 1.7;
            margin-bottom: 40px;
        }

        /* feature pills */
        .features { display: flex; flex-direction: column; gap: 14px; width: 100%; }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 12px;
            padding: 13px 18px;
        }
        .feature-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
            color: #fff;
        }
        .feature-text { color: rgba(255,255,255,.9); font-size: .82rem; line-height: 1.4; }
        .feature-text strong { display: block; font-weight: 600; font-size: .88rem; margin-bottom: 2px; }

        /* cross/palang merah icon decorative */
        .medical-cross {
            position: absolute;
            opacity: .04;
            font-size: 180px;
            color: #fff;
            bottom: 30px;
            left: 30px;
            line-height: 1;
            transform: rotate(-15deg);
        }

        /* ── RIGHT PANEL ─────────────────────────────── */
        .right-panel {
            width: 440px;
            flex-shrink: 0;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 44px;
            box-shadow: -10px 0 60px rgba(0,0,0,.08);
            position: relative;
        }

        .right-header { text-align: center; margin-bottom: 32px; }
        .right-header .badge-gov {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--green-pale);
            color: var(--green-dark);
            font-size: .72rem;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 50px;
            margin-bottom: 18px;
            border: 1px solid #b3e8c6;
            letter-spacing: .4px;
        }
        .right-header h2 {
            color: var(--text-dark);
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .right-header p {
            color: var(--text-muted);
            font-size: .82rem;
        }

        /* Divider */
        .divider {
            width: 50px; height: 3px;
            background: linear-gradient(90deg, var(--green-main), var(--green-light));
            border-radius: 3px;
            margin: 10px auto 0;
        }

        /* Alerts */
        .alert-error {
            background: #fff5f5;
            border: 1px solid #fcd0d0;
            border-left: 4px solid var(--red-accent);
            border-radius: 10px;
            color: #a00;
            font-size: .82rem;
            padding: 11px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-inactive {
            background: #fffbeb;
            border: 1px solid #fde98a;
            border-left: 4px solid #f59e0b;
            border-radius: 10px;
            color: #854d0e;
            font-size: .82rem;
            padding: 11px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            color: var(--text-dark);
            font-size: .82rem;
            font-weight: 600;
            margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        .input-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #aac8b0;
            font-size: 1rem;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            border: 1.5px solid #d8ead8;
            border-radius: 10px;
            padding: 11px 42px;
            font-family: 'Poppins', sans-serif;
            font-size: .88rem;
            color: var(--text-dark);
            background: #fafffe;
            outline: none;
            transition: border-color .25s, box-shadow .25s;
        }
        .input-wrap input:focus {
            border-color: var(--green-main);
            box-shadow: 0 0 0 3px rgba(0,165,56,.12);
            background: #fff;
        }
        .input-wrap input.is-invalid { border-color: #e74c3c; }
        .input-wrap input::placeholder { color: #b0c8b0; }

        .toggle-pass {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #aac8b0;
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            transition: color .2s;
        }
        .toggle-pass:hover { color: var(--green-main); }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--green-dark), var(--green-main));
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: .3px;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 6px 20px rgba(0,165,56,.35);
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(0,165,56,.45);
        }
        .btn-login:active { transform: translateY(0); }

        /* footer */
        .right-footer {
            position: absolute;
            bottom: 20px;
            color: #b0c4b0;
            font-size: .72rem;
            text-align: center;
            width: 100%;
        }
        .right-footer span { color: var(--green-main); font-weight: 600; }

        /* Responsive */
        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; box-shadow: none; padding: 40px 28px; }
        }
    </style>
</head>
<body>

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="circle-deco c1"></div>
        <div class="circle-deco c2"></div>
        <div class="circle-deco c3"></div>
        <div class="medical-cross">&#10010;</div>

        <div class="left-content">
            <div class="logo-dinkes">
                <img src="{{ asset('assets/img/logodinkes.png') }}" alt="Logo Dinas Kesehatan Kota Batam">
            </div>

            <h1>SIPINJAM <span>Dinas Kesehatan</span> Kota Batam</h1>
            <p>Sistem Informasi Peminjaman Ruangan dan Barang untuk pengelolaan pinjam-pakai aset dan fasilitas Dinas Kesehatan Kota Batam secara tertib dan transparan.</p>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-box-seam"></i></div>
                    <div class="feature-text">
                        <strong>Peminjaman Barang</strong>
                        Ajukan dan kelola peminjaman inventaris dengan mudah
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-arrow-left-right"></i></div>
                    <div class="feature-text">
                        <strong>Pengembalian Barang</strong>
                        Catat pengembalian barang tepat waktu secara digital
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-clipboard2-check"></i></div>
                    <div class="feature-text">
                        <strong>Laporan &amp; Rekap</strong>
                        Pantau status barang dan riwayat peminjaman secara real-time
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div style="width:100%">
            <div class="right-header">
                <div class="badge-gov">
                    <i class="bi bi-box-seam"></i>
                    SISTEM PEMINJAMAN BARANG
                </div>
                <h2>Selamat Datang</h2>
                <p>Masuk menggunakan akun yang telah terdaftar</p>
                <div class="divider"></div>
            </div>

            {{-- Alert: akun tidak aktif --}}
            @if ($errors->has('inactive'))
                <div class="alert-inactive">
                    <i class="bi bi-slash-circle-fill" style="font-size:1.05rem;flex-shrink:0"></i>
                    <span>{{ $errors->first('inactive') }}</span>
                </div>
            @endif

            {{-- Alert: email/password salah --}}
            @if ($errors->has('email') && !$errors->has('inactive'))
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:1.05rem;flex-shrink:0"></i>
                    <span>Email atau password yang Anda masukkan salah.</span>
                </div>
            @endif

            <form action="{{ route('loginproses') }}" method="POST" novalidate>
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="bi bi-envelope-fill" style="color:var(--green-main);margin-right:5px"></i>
                        Alamat Email
                    </label>
                    <div class="input-wrap">
                        <i class="bi bi-at input-icon"></i>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="{{ $errors->has('email') && !$errors->has('inactive') ? 'is-invalid' : '' }}"
                               autocomplete="email" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="bi bi-lock-fill" style="color:var(--green-main);margin-right:5px"></i>
                        Password
                    </label>
                    <div class="input-wrap">
                        <i class="bi bi-key input-icon"></i>
                        <input type="password" id="password" name="password"
                               placeholder="Masukkan password Anda"
                               autocomplete="current-password" required>
                        <button type="button" class="toggle-pass" id="togglePass" title="Tampilkan/Sembunyikan Password">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        <div class="right-footer">
            &copy; {{ date('Y') }} &nbsp;<span>Dinas Kesehatan Kota Batam</span> &nbsp;· Hak cipta dilindungi
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePass').addEventListener('click', function () {
            const pwd  = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        // Loading state on submit
        document.querySelector('form').addEventListener('submit', function () {
            const btn = document.getElementById('btnLogin');
            btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;display:inline-block;animation:spin .7s linear infinite;margin-right:8px"></span> Memproses...';
            btn.disabled = true;
        });
    </script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

</body>
</html>