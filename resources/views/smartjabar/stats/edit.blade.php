@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Statistik OPD</h1>
        <p>Perbarui data penggunaan SmartJabar per OPD</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom:1rem;">
        <ul style="margin:0; padding-left:1rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('smartjabar.stats.update', $stat->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>OPD</label>
        <input type="text"
               class="form-control"
               value="{{ old('opd_name', $stat->opd->name ?? '-') }}"
               readonly>
        <input type="hidden" name="opd_id" value="{{ old('opd_id', $stat->opd_id) }}">
        <small style="color:#64748b;">OPD dikunci agar konsisten dengan data relasi.</small>
    </div>

    <div class="form-group">
        <label>Bulan</label>
        <select name="month" class="form-control" required>
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ (int) old('month', $stat->month) === $m ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
            @endfor
        </select>
    </div>

    <div class="form-group">
        <label>Tahun</label>
        <input type="number"
               name="year"
               class="form-control"
               min="2000"
               max="2099"
               value="{{ old('year', $stat->year) }}"
               required>
    </div>

    <div class="form-group">
        <label>Total ASN</label>
        <input type="number"
               name="total_asn"
               class="form-control"
               min="0"
               value="{{ old('total_asn', $stat->total_asn) }}"
               required>
    </div>

    <div class="form-group">
        <label>User Aktif</label>
        <input type="number"
               name="active_users"
               class="form-control"
               min="0"
               value="{{ old('active_users', $stat->active_users) }}"
               required>
        <small style="color:#64748b;">Harus <= Total ASN.</small>
    </div>

    <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
        <button type="submit" class="btn btn-primary">Update Statistik</button>
        <a href="{{ route('smartjabar.joined-apps.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection