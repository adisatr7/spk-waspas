@extends('layouts.app')

@section('title', 'Perhitungan WASPAS')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <h4 class="fw-semibold mb-1">Perhitungan WASPAS Baru</h4>
        <p class="text-muted mb-0 small">
            Isi nilai kinerja setiap influencer berdasarkan kriteria yang sudah ditetapkan.
        </p>
    </div>
    <a href="{{ route('staff.waspas.index') }}" class="btn btn-secondary btn-sm">
        &larr; Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($criteria->isNotEmpty())
    <div class="row g-3 mb-3">
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
                                @foreach ($criteria as $index => $c)
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
                                @php $totalPercentage = 0; @endphp
                                @foreach ($criteria as $index => $c)
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

<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('staff.waspas.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small">Judul Perhitungan</label>
                    <input type="text" name="title" class="form-control form-control-sm"
                           value="{{ old('title') }}" placeholder="Misal: Seleksi Endorse Campaign Ramadhan" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Deskripsi (opsional)</label>
                    <input type="text" name="description" class="form-control form-control-sm"
                           value="{{ old('description') }}"
                           placeholder="Keterangan singkat perhitungan">
                </div>
            </div>

            <div class="alert alert-warning small">
                <strong>Petunjuk:</strong> 
                <ul class="mb-0">
                    <li>Isi nilai setiap influencer untuk masing-masing kriteria.</li>
                    <li>Untuk kriteria <strong>benefit</strong>, nilai lebih besar berarti lebih baik.</li>
                    <li>Untuk kriteria <strong>cost</strong>, nilai lebih kecil berarti lebih baik.</li>
                </ul>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered align-middle table-sm">
                    <thead class="table-light">
                    <tr>
                        <th rowspan="2" style="min-width: 200px;">Influencer</th>
                        @foreach ($criteria as $c)
                            <th>
                                {{ $c->code }}<br>
                                <small>{{ $c->name }}</small><br>
                                <small>
                                    ({{ $c->type === 'benefit' ? 'Benefit' : 'Cost' }},
                                    w = {{ number_format($c->weight, 4) }})
                                </small>
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($influencers as $inf)
                        <tr>
                            <td>
                                <strong>{{ $inf->name }}</strong><br>
                                <small class="text-muted">
                                    {{ $inf->platform }}{{ $inf->username ? ' - @'.$inf->username : '' }}
                                </small>
                            </td>
                            @foreach ($criteria as $c)
                                <td style="width: 140px;">
                                    <input
                                        type="number"
                                        step="0.0001"
                                        name="scores[{{ $inf->id }}][{{ $c->id }}]"
                                        class="form-control form-control-sm"
                                        value="{{ old('scores.'.$inf->id.'.'.$c->id) }}"
                                        required
                                    >
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    Hitung & Simpan Hasil WASPAS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
