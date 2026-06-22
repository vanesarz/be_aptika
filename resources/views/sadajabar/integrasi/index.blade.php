{{-- ============================================================ --}}
{{-- FILE: resources/views/sadajabar/integrasi/index.blade.php  --}}
{{-- ============================================================ --}}
@extends('layouts.app')

@section('title', 'Integrasi Aplikasi — SaDAJabar')

@section('content')
<div class="page-header">
    <div>
        <h1>Integrasi Aplikasi</h1>
        <p>Data integrasi aplikasi SaDAJabar per kategori institusi</p>
    </div>
    <a href="{{ route('sadajabar.integrasi.create') }}" class="btn btn-primary">+ Tambah Data</a>
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
                <th>Kategori Institusi</th>
                <th>Jumlah App</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td><span style="color:var(--muted);font-family:var(--mono);font-size:.8rem">{{ $loop->iteration }}</span></td>
                <td><span class="badge">{{ $item->year }}</span></td>
                <td>{{ DateTime::createFromFormat('!m', $item->month)->format('F') }}</td>
                <td>{{ $item->institutionCategory->name ?? '-' }}</td>
                <td style="font-family:var(--mono)">{{ number_format($item->app_count) }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('sadajabar.integrasi.edit', $item->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form action="{{ route('sadajabar.integrasi.destroy', $item->id) }}" method="POST"
                              onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row">
                <td colspan="6">Belum ada data. <a href="{{ route('sadajabar.integrasi.create') }}" style="color:var(--accent-2)">Tambah sekarang</a></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection