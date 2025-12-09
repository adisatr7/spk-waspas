<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SPK Influencer WASPAS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            font-family: 'Outfit', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;

            /* Terang + Soft Gradient */
            background:
                radial-gradient(circle at 0% 0%, rgba(56,189,248,.25) 0, transparent 55%),
                radial-gradient(circle at 100% 0%, rgba(168,85,247,.25) 0, transparent 55%),
                radial-gradient(circle at 0% 100%, rgba(52,211,153,.25) 0, transparent 55%),
                #e2e8f0;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 430px;
        }

        .auth-card {
            background: rgba(255,255,255,0.87);
            padding: 35px 28px;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.14);
            backdrop-filter: blur(7px);
            animation: fadeIn .4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .brand-badge {
            font-size: 0.78rem;
            letter-spacing: 2px;
            color: #64748b;
            text-transform: uppercase;
        }

        .auth-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
        }

        .auth-subtitle {
            font-size: .9rem;
            color: #475569;
        }

        .form-label {
            font-size: .9rem;
            font-weight: 500;
            color: #334155;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            font-size: .92rem;
        }

        .form-control:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px rgba(56,189,248,.55);
            background: #fff;
        }

        .auth-btn {
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
            border: none;
            padding: 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
        }

        .auth-btn:hover {
            opacity: .95;
        }

        .auth-footer {
            font-size: .9rem;
            color: #475569;
        }

        .auth-footer a {
            color: #0ea5e9;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            font-size: .85rem;
        }
    </style>
</head>

<body>

<div class="auth-wrapper">
    <div class="auth-card">

        {{-- HEADER --}}
        <div class="text-center mb-3">
            <div class="brand-badge mb-1">SPK Influencer • WASPAS</div>
            <div class="auth-title">Masuk</div>
            <div class="auth-subtitle">Login untuk mengakses sistem Staff dan Manager</div>
        </div>

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM LOGIN --}}
        <form action="{{ route('login.process') }}" method="POST" class="mt-3">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control"
                    placeholder="nama@email.com"
                    required
                    autofocus
                >
            </div>

            <div class="mb-2">
                <label class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="••••••••"
                    required
                >
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label for="remember" class="form-check-label">Ingat saya</label>
            </div>

            <button class="btn auth-btn w-100">Login</button>
        </form>



    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
