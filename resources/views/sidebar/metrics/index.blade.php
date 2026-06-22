@extends('layouts.app')
@section('title', 'Sidebar - Metrics')
@section('content')
<div class="page-header"><div><h1>Metrics</h1></div><a href="{{ route('sidebar.metrics.create') }}" class="btn btn-primary">+ Tambah</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Service</th><th>Periode</th><th>Total Users</th><th>Active Users</th><th>Docs</th><th>Aksi</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->id }}</td><td>{{ $item->service_type_id }}</td><td>{{ $item->month }}/{{ $item->year }}</td><td>{{ $item->total_users }}</td><td>{{ $item->active_users }}</td><td>{{ $item->document_created }}</td><td><div class="actions"><a class="btn btn-secondary btn-sm" href="{{ route('sidebar.metrics.edit',$item->id) }}">Edit</a><form method="POST" action="{{ route('sidebar.metrics.destroy',$item->id) }}">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form></div></td></tr>
@empty <tr><td colspan="7">Belum ada data.</td></tr>@endforelse
</tbody></table></div>
@endsection
