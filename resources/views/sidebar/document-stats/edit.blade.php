@extends('layouts.app')
@section('title', 'Edit Document Stat')
@section('content')
<div class="card"><form method="POST" action="{{ route('sidebar.document-stats.update',$item->id) }}">@csrf @method('PUT')
<div class="form-group"><label>Document Type ID</label><input type="number" name="document_type_id" value="{{ old('document_type_id',$item->document_type_id) }}"></div>
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month',$item->month) }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',$item->year) }}"></div>
<div class="form-group"><label>Total Count</label><input type="number" name="total_count" min="0" value="{{ old('total_count',$item->total_count) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Perbarui</button><a class="btn btn-secondary" href="{{ route('sidebar.document-stats.index') }}">Batal</a></div>
</form></div>
@endsection
