@extends('layouts.app')

@section('title', 'Edit Application Replication')

@section('content')
<div class="page-header"><div><h1>Edit Application Replication</h1></div></div>
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card"><form method="POST" action="{{ route('rekayasa.application-replications.update',$item->id) }}">@csrf @method('PUT')
<div class="form-group"><label>Institution ID</label><input type="number" name="institution_id" value="{{ old('institution_id',$item->institution_id) }}"></div>
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month',$item->month) }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',$item->year) }}"></div>
<div class="form-group"><label>Total Replications</label><input type="number" name="total_replications" min="0" value="{{ old('total_replications',$item->total_replications) }}"></div>
<div class="form-actions"><button class="btn btn-primary" type="submit">Perbarui</button><a class="btn btn-secondary" href="{{ route('rekayasa.application-replications.index') }}">Batal</a></div>
</form></div>
@endsection
