@extends('layouts.app')
@section('title', 'Sidebar - Document Stats')
@section('content')
<div class="page-header"><div><h1>Document Stats</h1></div><a href="{{ route('sidebar.document-stats.create') }}" class="btn btn-primary">+ Tambah</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Service</th><th>Doc Type</th><th>Periode</th><th>Total</th><th>Aksi</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->id }}</td><td>{{ $item->service_type_id }}</td><td>{{ $item->document_type_id }}</td><td>{{ $item->month }}/{{ $item->year }}</td><td>{{ $item->total_count }}</td><td><div class="actions"><a class="btn btn-secondary btn-sm" href="{{ route('sidebar.document-stats.edit',$item->id) }}">Edit</a><form method="POST" action="{{ route('sidebar.document-stats.destroy',$item->id) }}">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form></div></td></tr>
@empty <tr><td colspan="6">Belum ada data.</td></tr>@endforelse
</tbody></table></div>
@endsection
