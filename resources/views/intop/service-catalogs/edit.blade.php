@extends('layouts.app')
@section('title', 'Edit Service Catalog')
@section('content')
<div class="card"><form method="POST" action="{{ route('intop.service-catalogs.update',$item->id) }}">@csrf @method('PUT')
<div class="form-group"><label>Category</label><input type="text" name="category" value="{{ old('category',$item->category) }}"></div>
<div class="form-group"><label>Service Name</label><input type="text" name="service_name" value="{{ old('service_name',$item->service_name) }}"></div>
<div class="form-group"><label>Year</label><input type="number" name="year" min="2000" max="2099" value="{{ old('year',$item->year) }}"></div>
<div class="form-actions"><button class="btn btn-primary">Perbarui</button><a class="btn btn-secondary" href="{{ route('intop.service-catalogs.index') }}">Batal</a></div>
</form></div>
@endsection
