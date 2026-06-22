@extends('layouts.app')
@section('title', 'Rekayasa - Mentoring Performances')
@section('content')
<div class="page-header"><div><h1>Mentoring Performances</h1></div><a href="{{ route('rekayasa.mentoring-performances.create') }}" class="btn btn-primary">+ Tambah</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Service</th><th>Periode</th><th>Total Apps</th><th>Target</th><th>Realisasi</th><th>Aksi</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->id }}</td><td>{{ $item->service_type_id }}</td><td>{{ $item->month }}/{{ $item->year }}</td><td>{{ $item->total_apps }}</td><td>{{ $item->target }}</td><td>{{ $item->realization }}</td><td><div class="actions"><a class="btn btn-secondary btn-sm" href="{{ route('rekayasa.mentoring-performances.edit',$item->id) }}">Edit</a><form method="POST" action="{{ route('rekayasa.mentoring-performances.destroy',$item->id) }}" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form></div></td></tr>
@empty <tr><td colspan="7">Belum ada data.</td></tr>@endforelse
</tbody></table></div>
@endsection
