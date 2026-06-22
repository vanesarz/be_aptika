@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1>Input Massal Statistik OPD</h1>
        <p>Isi data penggunaan untuk periode tertentu (Excel Mode)</p>
    </div>
</div>

<form action="{{ route('smartjabar.stats.store') }}" method="POST">
    @csrf
    
    {{-- Pengaturan Periode Global --}}
    <div class="period-picker">
        <div class="form-group">
            <label>Bulan</label>
            <select name="month" class="form-control">
                @for ($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label>Tahun</label>
            <input type="number" name="year" class="form-control" value="{{ date('Y') }}">
        </div>
    </div>

    <div class="table-wrap" style="margin-top: 2rem;">
        <table class="excel-table">
            <thead>
                <tr>
                    <th>Nama OPD / Instansi</th>
                    <th width="200">Total ASN</th>
                    <th width="200">User Aktif</th>
                </tr>
            </thead>
            <tbody>
                @foreach($opds as $opd)
                <tr>
                    <td class="opd-name">{{ $opd }}</td>
                    <td>
                        <input type="number" name="stats[{{ $opd }}][total_asn]" 
                               class="input-cell" value="0" min="0">
                    </td>
                    <td>
                        <input type="number" name="stats[{{ $opd }}][active_users]" 
                               class="input-cell" value="0" min="0">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="sticky-actions">
        <button type="submit" class="btn btn-primary btn-lg">Simpan Semua Data ({{ count($opds) }} OPD)</button>
    </div>
</form>

<style>
    .period-picker {
        display: flex;
        gap: 2rem;
        background: #4296eb;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    .excel-table {
        width: 100%;
        border-collapse: collapse;
        background: rgb(0, 0, 0);
    }

    .excel-table th {
        background: var(--sj-blue-base);
        color: rgb(255, 255, 255);
        text-align: left;
        padding: 12px;
        position: sticky;
        top: 0;
    }

    .opd-name {
        font-weight: 600;
        font-size: 0.85rem;
        background: #000000;
        border-right: 1px solid #e2e8f0;
    }

    .excel-table td {
        border: 1px solid #e2e8f0;
        padding: 0;
    }

    .input-cell {
        width: 100%;
        border: none;
        padding: 12px;
        font-family: var(--sj-mono);
        outline: none;
        transition: background 0.2s;
    }

    .input-cell:focus {
        background: #fff7ed;
        box-shadow: inset 0 0 0 2px var(--sj-blue-light);
    }

    .sticky-actions {
        position: sticky;
        bottom: 20px;
        margin-top: 2rem;
        text-align: right;
        background: rgba(255,255,255,0.9);
        padding: 1rem;
        border-radius: 12px;
        box-shadow: 0 -10px 20px rgba(0,0,0,0.05);
    }
</style>
@endsection