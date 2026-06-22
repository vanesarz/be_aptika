@extends('layouts.app')

@section('title', 'Tambah Application Replication')

@section('content')
<div class="page-header"><div><h1>Tambah Application Replication</h1></div></div>
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card"><form method="POST" action="{{ route('rekayasa.application-replications.store') }}">@csrf
<div class="form-group"><label>Institution ID</label><input type="number" name="institution_id" value="{{ old('institution_id') }}"></div>
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month') }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year', date('Y')) }}"></div>
<div class="form-group"><label>Total Replications</label><input type="number" name="total_replications" min="0" value="{{ old('total_replications',0) }}"></div>
<div class="form-actions"><button class="btn btn-primary" type="submit">Simpan</button><a class="btn btn-secondary" href="{{ route('rekayasa.application-replications.index') }}">Batal</a></div>
</form></div>
@endsection
