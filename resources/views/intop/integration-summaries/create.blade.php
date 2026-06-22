@extends('layouts.app')
@section('title', 'Tambah Integration Summary')
@section('content')
<div class="card"><form method="POST" action="{{ route('intop.integration-summaries.store') }}">@csrf
<div class="form-group"><label>Institution ID</label><input type="number" name="institution_id" value="{{ old('institution_id') }}"></div>
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month') }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',date('Y')) }}"></div>
<div class="form-group"><label>App Count</label><input type="number" name="app_count" min="0" value="{{ old('app_count',0) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Simpan</button><a class="btn btn-secondary" href="{{ route('intop.integration-summaries.index') }}">Batal</a></div>
</form></div>
@endsection
