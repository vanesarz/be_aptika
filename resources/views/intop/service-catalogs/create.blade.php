@extends('layouts.app')
@section('title', 'Tambah Service Catalog')
@section('content')
<div class="card"><form method="POST" action="{{ route('intop.service-catalogs.store') }}">@csrf
<div class="form-group"><label>Category</label><input type="text" name="category" value="{{ old('category') }}"></div>
<div class="form-group"><label>Service Name</label><input type="text" name="service_name" value="{{ old('service_name') }}"></div>
<div class="form-group"><label>Year</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',date('Y')) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Simpan</button><a class="btn btn-secondary" href="{{ route('intop.service-catalogs.index') }}">Batal</a></div>
</form></div>
@endsection
