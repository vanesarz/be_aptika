@extends('layouts.app')
@section('title', 'Tambah OPD Usage')
@section('content')
<div class="card"><form method="POST" action="{{ route('sidebar.opd-usages.store') }}">@csrf
<div class="form-group"><label>OPD ID</label><input type="number" name="opd_id" value="{{ old('opd_id') }}"></div>
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month') }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',date('Y')) }}"></div>
<div class="form-group"><label>Active Count</label><input type="number" name="active_count" min="0" value="{{ old('active_count',0) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Simpan</button><a class="btn btn-secondary" href="{{ route('sidebar.opd-usages.index') }}">Batal</a></div>
</form></div>
@endsection
