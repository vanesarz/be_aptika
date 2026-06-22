@extends('layouts.app')

@section('title', 'Dashboard SmartJabar')

@section('content')

{{-- 1. HEADER HALAMAN --}}
<div class="page-header">
    <div>
        <h1>SmartJabar Dashboard</h1>
        <p>Ringkasan data aplikasi dan statistik penggunaan OPD</p>
    </div>
    <div class="actions">
        <a href="{{ route('smartjabar.joined-apps.create') }}" class="btn btn-secondary">
            + Apps
        </a>
        <a href="{{ route('smartjabar.stats.create') }}" class="btn btn-primary">
            + Statistik OPD
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- 2. SEKSI JOINED APPS --}}
<div style="margin-bottom: 3rem;">
    <h2 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--accent);">
        <span style="font-family: var(--mono); opacity: 0.5;">01.</span> Joined Apps History
    </h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tahun</th>
                    <th>Bulan</th>
                    <th>Total Apps</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td><span style="color:var(--muted);font-family:var(--mono);font-size:.8rem">{{ $loop->iteration }}</span></td>
                    <td><span class="badge">{{ $item->year }}</span></td>
                    <td>{{ DateTime::createFromFormat('!m', $item->month)->format('F') }}</td>
                    <td style="font-family:var(--mono)">{{ number_format($item->total_apps) }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('smartjabar.joined-apps.edit', $item->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form action="{{ route('smartjabar.joined-apps.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="5">Belum ada data aplikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<hr style="border: 0; border-top: 1px solid var(--border); margin-bottom: 3rem;">

{{-- 3. SEKSI USAGE STATS (DATA BARU) --}}
<div>
    <h2 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--accent-2);">
        <span style="font-family: var(--mono); opacity: 0.5;">02.</span> OPD Usage Statistics
    </h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama OPD / Badan</th>
                    <th>Periode</th>
                    <th>Total ASN</th>
                    <th>User Aktif</th>
                    <th>'% PENGGUNA SMARTJABAR</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats as $stat)
                <tr>
                    <td style="font-weight: 600; font-size: 0.8rem;">{{ $stat->opd->name ?? '-' }}</td>
                    <td>
                        <span style="color: var(--muted); font-size: 0.75rem;">
                            {{ DateTime::createFromFormat('!m', $stat->month)->format('M') }} {{ $stat->year }}
                        </span>
                    </td>
                    <td style="font-family: var(--mono);">{{ number_format($stat->total_asn) }}</td>
                    <td style="font-family: var(--mono); color: var(--accent);">{{ number_format($stat->active_users) }}</td>
                    <td>
                        @php 
                            $percentage = $stat->total_asn > 0 ? ($stat->active_users / $stat->total_asn) * 100 : 0;
                            $color = $percentage > 70 ? 'var(--accent)' : ($percentage > 40 ? 'var(--accent-2)' : 'var(--danger)');
                        @endphp
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="flex: 1; height: 4px; background: var(--border); border-radius: 2px; width: 60px;">
                                <div style="width: {{ $percentage }}%; height: 100%; background: {{ $color }}; border-radius: 2px;"></div>
                            </div>
                            <span style="font-family: var(--mono); font-size: 0.75rem; color: {{ $color }};">
                                {{ round($percentage) }}%
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('smartjabar.stats.edit', $stat->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form action="{{ route('smartjabar.stats.destroy', $stat->id) }}" method="POST" onsubmit="return confirm('Hapus statistik ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="6">Belum ada statistik OPD.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection