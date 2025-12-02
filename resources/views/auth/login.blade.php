@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="card card-auth shadow-sm">
    <div class="card-body">
        <h4 class="mb-3 text-center">Login</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    required
                >
            </div>

            <div class="mb-3 form-check">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    class="form-check-input"
                >
                <label for="remember" class="form-check-label">Ingat saya</label>
            </div>

            <button class="btn btn-primary w-100" type="submit">Login</button>

            <p class="mt-3 mb-0 text-center">
                Belum punya akun?
                <a href="{{ route('register') }}">Register di sini</a>
            </p>
        </form>
    </div>
</div>
@endsection
