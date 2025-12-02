@extends('layouts.app')

@section('title', 'Dashboard Manager')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4>Dashboard Manager</h4>
        <p>Selamat datang, {{ auth()->user()->name }} (Manager)</p>
        <p>Nanti di sini akan ada menu: kelola staff, lihat hasil WASPAS, dll.</p>
    </div>
</div>
@endsection
