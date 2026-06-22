@extends('layouts.app')
@section('title', 'Intop - Integration Summaries')
@section('content')
<div class="page-header"><div><h1>Integration Summaries</h1></div><a href="{{ route('intop.integration-summaries.create') }}" class="btn btn-primary">+ Tambah</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Service</th><th>Institution</th><th>Periode</th><th>App Count</th><th>Aksi</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->id }}</td><td>{{ $item->service_type_id }}</td><td>{{ $item->institution_id }}</td><td>{{ $item->month }}/{{ $item->year }}</td><td>{{ $item->app_count }}</td><td><div class="actions"><a class="btn btn-secondary btn-sm" href="{{ route('intop.integration-summaries.edit',$item->id) }}">Edit</a><form method="POST" action="{{ route('intop.integration-summaries.destroy',$item->id) }}">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form></div></td></tr>
@empty <tr><td colspan="6">Belum ada data.</td></tr>@endforelse
</tbody></table></div>
@endsection
