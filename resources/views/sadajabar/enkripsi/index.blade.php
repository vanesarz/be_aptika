{{-- ============================================================ --}}
{{-- FILE: resources/views/sadajabar/enkripsi/index.blade.php   --}}
{{-- ============================================================ --}}
@extends('layouts.app')

@section('title', 'Statistik Enkripsi — SaDAJabar')

@section('content')
<div class="page-header">
    <div>
        <h1>Statistik Enkripsi</h1>
        <p>Data statistik enkripsi SaDAJabar per bulan</p>
    </div>
    <a href="{{ route('sadajabar.enkripsi.create') }}" class="btn btn-primary">+ Tambah Data</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Jumlah Aplikasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td><span style="color:var(--muted);font-family:var(--mono);font-size:.8rem">{{ $loop->iteration }}</span></td>
                <td><span class="badge">{{ $item->year }}</span></td>
                <td>{{ DateTime::createFromFormat('!m', $item->month)->format('F') }}</td>
                <td style="font-family:var(--mono)">{{ number_format($item->app_count) }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('sadajabar.enkripsi.edit', $item->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form action="{{ route('sadajabar.enkripsi.destroy', $item->id) }}" method="POST"
                              onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row">
                <td colspan="5">Belum ada data. <a href="{{ route('sadajabar.enkripsi.create') }}" style="color:var(--accent-2)">Tambah sekarang</a></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection