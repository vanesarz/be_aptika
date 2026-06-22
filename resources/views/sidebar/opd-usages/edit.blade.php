@extends('layouts.app')
@section('title', 'Edit OPD Usage')
@section('content')
<div class="card"><form method="POST" action="{{ route('sidebar.opd-usages.update',$item->id) }}">@csrf @method('PUT')
<div class="form-group"><label>OPD ID</label><input type="number" name="opd_id" value="{{ old('opd_id',$item->opd_id) }}"></div>
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month',$item->month) }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',$item->year) }}"></div>
<div class="form-group"><label>Active Count</label><input type="number" name="active_count" min="0" value="{{ old('active_count',$item->active_count) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Perbarui</button><a class="btn btn-secondary" href="{{ route('sidebar.opd-usages.index') }}">Batal</a></div>
</form></div>
@endsection
