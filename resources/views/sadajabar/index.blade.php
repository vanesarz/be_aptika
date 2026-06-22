{{-- ============================================================ --}}
{{-- FILE: resources/views/sadajabar/index.blade.php            --}}
{{-- ============================================================ --}}
@extends('layouts.app')

@section('title', 'SaDAJabar — Enkripsi & Integrasi')

@section('content')
<div class="page-header">
    <div>
        <h1>SaDAJabar</h1>
        <p>Data statistik enkripsi dan integrasi aplikasi</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- TAB NAVIGATION --}}
<div class="tab-nav">
    <button class="tab-btn active" onclick="switchTab('enkripsi', this)">Statistik Enkripsi</button>
    <button class="tab-btn" onclick="switchTab('integrasi', this)">Integrasi Aplikasi</button>
</div>

{{-- ── TAB ENKRIPSI ── --}}
<div id="tab-enkripsi" class="tab-panel">
    <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
        <a href="{{ route('sadajabar.enkripsi.create') }}" class="btn btn-primary">+ Tambah Data Enkripsi</a>
    </div>
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
                @forelse($enkripsi as $item)
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
</div>

{{-- ── TAB INTEGRASI ── --}}
<div id="tab-integrasi" class="tab-panel" style="display:none">
    <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
        <a href="{{ route('sadajabar.integrasi.create') }}" class="btn btn-primary">+ Tambah Data Integrasi</a>
    </div>
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
                @forelse($integrasi as $item)
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
</div>

<style>
.tab-nav {
    display: flex;
    gap: .5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--border);
    padding-bottom: 0;
}
.tab-btn {
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--muted);
    font-family: var(--sans);
    font-size: .875rem;
    font-weight: 600;
    padding: .6rem 1.1rem;
    cursor: pointer;
    margin-bottom: -1px;
    transition: color .15s, border-color .15s;
}
.tab-btn:hover { color: var(--text); }
.tab-btn.active { color: var(--accent-2); border-bottom-color: var(--accent-2); }
</style>

<script>
function switchTab(name, el) {
    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    el.classList.add('active');
}

// Kalau redirect balik dari create/edit, buka tab yang sesuai
const hash = window.location.hash;
if (hash === '#integrasi') {
    switchTab('integrasi', document.querySelectorAll('.tab-btn')[1]);
}
</script>
@endsection