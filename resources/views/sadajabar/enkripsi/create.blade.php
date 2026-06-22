{{-- ============================================================ --}}
{{-- FILE: resources/views/sadajabar/enkripsi/create.blade.php  --}}
{{-- ============================================================ --}}
@extends('layouts.app')

@section('title', 'Tambah Enkripsi — SaDAJabar')

@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Data Enkripsi</h1>
        <p>SaDAJabar — Encryption Stats</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Terdapat kesalahan input:</strong>
        <ul style="margin-top:.4rem;padding-left:1.25rem">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="card">
    <form action="{{ route('sadajabar.enkripsi.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="year">Tahun</label>
            <input type="number" id="year" name="year"
                   value="{{ old('year', date('Y')) }}" min="2000" max="2099"
                   class="{{ $errors->has('year') ? 'is-invalid' : '' }}">
            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="month">Bulan</label>
            <select id="month" name="month" class="{{ $errors->has('month') ? 'is-invalid' : '' }}">
                <option value="">-- Pilih Bulan --</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endforeach
            </select>
            @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="app_count">Jumlah Aplikasi</label>
            <input type="number" id="app_count" name="app_count"
                   value="{{ old('app_count', 0) }}" min="0"
                   class="{{ $errors->has('app_count') ? 'is-invalid' : '' }}">
            @error('app_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('sadajabar.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection