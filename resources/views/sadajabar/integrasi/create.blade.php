{{-- ============================================================ --}}
{{-- FILE: resources/views/sadajabar/integrasi/create.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')

@section('title', 'Tambah Integrasi — SaDAJabar')

@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Data Integrasi</h1>
        <p>SaDAJabar — App Integrations</p>
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
    <form action="{{ route('sadajabar.integrasi.store') }}" method="POST">
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
            <label for="sadajabar_institution_categories_id">Kategori Institusi</label>
            <select id="sadajabar_institution_categories_id"
                    name="sadajabar_institution_categories_id"
                    class="{{ $errors->has('sadajabar_institution_categories_id') ? 'is-invalid' : '' }}">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('sadajabar_institution_categories_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('sadajabar_institution_categories_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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