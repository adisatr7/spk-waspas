@extends('layouts.app')

@section('title', 'Kriteria & Bobot')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-semibold mb-1">Kriteria & Bobot</h4>
        <p class="text-muted mb-0 small">
            Kelola kriteria yang digunakan pada perhitungan WASPAS beserta bobotnya.
        </p>
    </div>
    <a href="{{ route('staff.criteria.create') }}" class="btn btn-primary btn-sm">
        + Tambah Kriteria
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@php
    /** @var \Illuminate\Support\Collection|\App\Models\Criterion[] $allCriteria */
    $allCriteria = $allCriteria ?? collect();
@endphp

@if ($allCriteria->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-warning bg-opacity-10 border-0 rounded-top">
                    <h6 class="fw-semibold mb-0">Data Kriteria</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Kriteria</th>
                                    <th style="width: 70px;">Kode</th>
                                    <th style="width: 90px;">Atribut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allCriteria as $index => $c)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $c->name }}</td>
                                        <td><strong>{{ $c->code }}</strong></td>
                                        <td>
                                            @if ($c->type === 'benefit')
                                                <span class="badge text-bg-success">Benefit</span>
                                            @else
                                                <span class="badge text-bg-danger">Cost</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-warning bg-opacity-10 border-0 rounded-top">
                    <h6 class="fw-semibold mb-0">Data Pembobotan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Kriteria</th>
                                    <th style="width: 70px;">Kode</th>
                                    <th style="width: 110px;">Bobot (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalPercentage = 0;
                                @endphp
                                @foreach ($allCriteria as $index => $c)
                                    @php
                                        $percentage = $c->weight * 100;
                                        $totalPercentage += $percentage;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $c->name }}</td>
                                        <td><strong>{{ $c->code }}</strong></td>
                                        <td>{{ rtrim(rtrim(number_format($percentage, 2), '0'), '.') }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th>{{ rtrim(rtrim(number_format($totalPercentage, 2), '0'), '.') }}%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3 p-md-4">

        {{-- Info total bobot --}}
        <div class="alert alert-info py-2 px-3 small">
            <strong>Total bobot aktif:</strong>
            {{ number_format($totalWeight, 4) }}
            (idealnya = 1.0000 untuk perhitungan WASPAS)
        </div>

        {{-- Form cari --}}
        <form method="GET" action="{{ route('staff.criteria.index') }}" class="row g-2 align-items-center mb-3">
            <div class="col-md-4">
                <input
                    type="text"
                    name="q"
                    value="{{ $search ?? '' }}"
                    class="form-control form-control-sm"
                    placeholder="Cari kode / nama kriteria..."
                >
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    Cari
                </button>
                <a href="{{ route('staff.criteria.index') }}" class="btn btn-link btn-sm">
                    Reset
                </a>
            </div>
        </form>

        @if ($criteria->isEmpty())
            <div class="alert alert-info mb-0">
                Belum ada kriteria. Klik <strong>Tambah Kriteria</strong> untuk menambahkan.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Jenis</th>
                            <th>Bobot</th>
                            <th>Aktif</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($criteria as $i => $c)
                            <tr>
                                <td>{{ $criteria->firstItem() + $i }}</td>
                                <td><strong>{{ $c->code }}</strong></td>
                                <td>{{ $c->name }}</td>
                                <td>
                                    @if ($c->type === 'benefit')
                                        <span class="badge bg-success">Benefit</span>
                                    @else
                                        <span class="badge bg-danger">Cost</span>
                                    @endif
                                </td>
                                <td>{{ number_format($c->weight, 4) }}</td>
                                <td>
                                    @if ($c->is_active)
                                        <span class="badge bg-primary">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('staff.criteria.edit', $c->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <form action="{{ route('staff.criteria.destroy', $c->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus kriteria ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $criteria->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
