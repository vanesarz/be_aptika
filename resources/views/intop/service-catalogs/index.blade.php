@extends('layouts.app')
@section('title', 'Intop - Service Catalogs')
@section('content')
<div class="page-header"><div><h1>Service Catalogs</h1></div><a href="{{ route('intop.service-catalogs.create') }}" class="btn btn-primary">+ Tambah</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Service</th><th>Category</th><th>Service Name</th><th>Year</th><th>Aksi</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->id }}</td><td>{{ $item->service_type_id }}</td><td>{{ $item->category }}</td><td>{{ $item->service_name }}</td><td>{{ $item->year }}</td><td><div class="actions"><a class="btn btn-secondary btn-sm" href="{{ route('intop.service-catalogs.edit',$item->id) }}">Edit</a><form method="POST" action="{{ route('intop.service-catalogs.destroy',$item->id) }}">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form></div></td></tr>
@empty <tr><td colspan="6">Belum ada data.</td></tr>@endforelse
</tbody></table></div>
@endsection
