@extends('layouts.app')
@section('title', 'Edit Metric')
@section('content')
<div class="card"><form method="POST" action="{{ route('sidebar.metrics.update',$item->id) }}">@csrf @method('PUT')
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month',$item->month) }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',$item->year) }}"></div>
<div class="form-group"><label>Total Users</label><input type="number" name="total_users" min="0" value="{{ old('total_users',$item->total_users) }}"></div>
<div class="form-group"><label>Active Users</label><input type="number" name="active_users" min="0" value="{{ old('active_users',$item->active_users) }}"></div>
<div class="form-group"><label>Document Created</label><input type="number" name="document_created" min="0" value="{{ old('document_created',$item->document_created) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Perbarui</button><a class="btn btn-secondary" href="{{ route('sidebar.metrics.index') }}">Batal</a></div>
</form></div>
@endsection
