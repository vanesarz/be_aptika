@extends('layouts.app')
@section('title', 'Edit Mentoring Performance')
@section('content')
<div class="card"><form method="POST" action="{{ route('rekayasa.mentoring-performances.update',$item->id) }}">@csrf @method('PUT')
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month',$item->month) }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',$item->year) }}"></div>
<div class="form-group"><label>Total Apps</label><input type="number" name="total_apps" min="0" value="{{ old('total_apps',$item->total_apps) }}"></div>
<div class="form-group"><label>Target</label><input type="number" name="target" min="0" value="{{ old('target',$item->target) }}"></div>
<div class="form-group"><label>Realisasi</label><input type="number" name="realization" min="0" value="{{ old('realization',$item->realization) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Perbarui</button><a class="btn btn-secondary" href="{{ route('rekayasa.mentoring-performances.index') }}">Batal</a></div>
</form></div>
@endsection
