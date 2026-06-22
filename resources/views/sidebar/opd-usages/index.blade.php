@extends('layouts.app')
@section('title', 'Sidebar - OPD Usages')
@section('content')
<div class="page-header"><div><h1>OPD Usages</h1></div><a href="{{ route('sidebar.opd-usages.create') }}" class="btn btn-primary">+ Tambah</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Service</th><th>OPD</th><th>Periode</th><th>Active</th><th>Aksi</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->id }}</td><td>{{ $item->service_type_id }}</td><td>{{ $item->opd_id }}</td><td>{{ $item->month }}/{{ $item->year }}</td><td>{{ $item->active_count }}</td><td><div class="actions"><a class="btn btn-secondary btn-sm" href="{{ route('sidebar.opd-usages.edit',$item->id) }}">Edit</a><form method="POST" action="{{ route('sidebar.opd-usages.destroy',$item->id) }}">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form></div></td></tr>
@empty <tr><td colspan="6">Belum ada data.</td></tr>@endforelse
</tbody></table></div>
@endsection
