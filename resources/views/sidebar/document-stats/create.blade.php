@extends('layouts.app')
@section('title', 'Tambah Document Stat')
@section('content')
<div class="card"><form method="POST" action="{{ route('sidebar.document-stats.store') }}">@csrf
<div class="form-group"><label>Document Type ID</label><input type="number" name="document_type_id" value="{{ old('document_type_id') }}"></div>
<div class="form-group"><label>Bulan</label><input type="number" name="month" min="1" max="12" value="{{ old('month') }}"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',date('Y')) }}"></div>
<div class="form-group"><label>Total Count</label><input type="number" name="total_count" min="0" value="{{ old('total_count',0) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Simpan</button><a class="btn btn-secondary" href="{{ route('sidebar.document-stats.index') }}">Batal</a></div>
</form></div>
@endsection
