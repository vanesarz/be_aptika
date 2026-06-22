@extends('layouts.app')
@section('title', 'Tambah Metric')
@section('content')
<div class="card"><form method="POST" action="{{ route('sidebar.metrics.store') }}">@csrf
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month') }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',date('Y')) }}"></div>
<div class="form-group"><label>Total Users</label><input type="number" name="total_users" min="0" value="{{ old('total_users',0) }}"></div>
<div class="form-group"><label>Active Users</label><input type="number" name="active_users" min="0" value="{{ old('active_users',0) }}"></div>
<div class="form-group"><label>Document Created</label><input type="number" name="document_created" min="0" value="{{ old('document_created',0) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Simpan</button><a class="btn btn-secondary" href="{{ route('sidebar.metrics.index') }}">Batal</a></div>
</form></div>
@endsection
