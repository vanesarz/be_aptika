@extends('layouts.app')

@section('title', 'Rekayasa - Application Replications')

@section('content')
<div class="page-header"><div><h1>Application Replications</h1></div><a href="{{ route('rekayasa.application-replications.create') }}" class="btn btn-primary">+ Tambah</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Service</th><th>Institution</th><th>Periode</th><th>Total</th><th>Aksi</th></tr></thead><tbody>
@forelse($items as $item)
<tr><td>{{ $item->id }}</td><td>{{ $item->service_type_id }}</td><td>{{ $item->institution_id }}</td><td>{{ $item->month }}/{{ $item->year }}</td><td>{{ $item->total_replications }}</td><td><div class="actions"><a class="btn btn-secondary btn-sm" href="{{ route('rekayasa.application-replications.edit',$item->id) }}">Edit</a><form method="POST" action="{{ route('rekayasa.application-replications.destroy',$item->id) }}" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" type="submit">Hapus</button></form></div></td></tr>
@empty <tr><td colspan="6">Belum ada data.</td></tr>
@endforelse
</tbody></table></div>
@endsection
