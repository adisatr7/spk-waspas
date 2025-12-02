@extends('layouts.app')

@section('title', 'Dashboard Staff')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4>Dashboard Staff</h4>
        <p>Selamat datang, {{ auth()->user()->name }} (Staff)</p>
        <p>Nanti di sini akan ada menu: kelola influencer, kriteria, perhitungan WASPAS, dll.</p>
    </div>
</div>
@endsection
