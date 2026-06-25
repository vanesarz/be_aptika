<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Perubahan TI - {{ $data->no_rfc }}</title>
    <style>
        @page {
            margin: 1.2cm 1.2cm 1.2cm 1.2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.5;
        }
        
        /* HEADER SURAT KOP 3 KOLOM */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
        }
        .header-logo {
            width: 15%;
            text-align: center;
        }
        .header-logo img {
            height: 55px;
            width: auto;
        }
        .header-title {
            width: 50%;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.4;
        }
        .header-meta {
            width: 35%;
            font-size: 12px;
            font-weight: normal;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            border: none !important;
            padding: 2px 0 !important;
            vertical-align: top;
        }

        /* TABEL UTAMA FORMULIR */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .main-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .section-title {
            background-color: #002160;
            font-weight: bold;
            color: #fff;
            font-size: 14px;
            padding: 4px 8px;
        }
        .w-col1 { width: 30%; font-weight: bold; }
        .w-col2 { width: 70%; }
        
        /* Checkbox Box */
        .cb-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            line-height: 10px;
            font-size: 10px;
            font-weight: bold;
            background-color: #fff;
            vertical-align: middle;
            margin-right: 5px;
            margin-top: 5px;
        }
        .cb-item {
            display: inline-block;
            margin-right: 15px;
            vertical-align: middle;
            line-height: 12px;
        }
        
        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .inner-table td {
            border: none !important;
            padding: 2px 0 !important;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .ttd-table td {
            border: 1px solid #000;
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }
        .ttd-space {
            height: 55px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <span style="font-size: 10px; color: #666; font-weight: bold;">LOGO</span>
            </td>
            
            <td class="header-title">
                FORMULIR PERMINTAAN PERUBAHAN TI<br>
            </td>
            
            <td class="header-meta">
                <table class="meta-table">
                    <tr>
                        <td style="width: 40%;">No. Dokumen</td>
                        <td style="width: 5%;">:</td>
                        <td style="width: 55%;">FR-015/KOM.03.05/ SANDIKA</td>
                    </tr>
                    <tr>
                        <td>No. Revisi</td>
                        <td>:</td>
                        <td>1.1</td>
                    </tr>
                    <tr>
                        <td>Tanggal Berlaku</td>
                        <td>:</td>
                        <td>1 Januari 2030</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <span style="font-size: 14px; font-weight: bold; text-transform: none; margin-bottom: 10px;">No. Registrasi: {{ $data->no_rfc }}</span>

    <table class="main-table">
        <tr>
            <td colspan="2" class="section-title">Informasi Pemohon</td>
        </tr>
        <tr>
            <td class="w-col1">Pemohon</td>
            <td class="w-col2">{{ $data->pemohon }}</td>
        </tr>
        <tr>
            <td class="w-col1">Unit Kerja</td>
            <td class="w-col2">{{ $data->unit_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Perangkat Daerah</td>
            <td class="w-col2">{{ $data->nama_perangkat_daerah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Nomor Surat/Contact Person</td>
            <td class="w-col2">{{ $data->nomor_kontak }}</td>
        </tr>

        <tr>
            <td colspan="2" class="section-title">Informasi Data</td>
        </tr>
        <tr>
            <td class="w-col1">Jenis Perubahan</td>
            <td class="w-col2">
                @foreach(['Permintaan Baru', 'Peningkatan Sistem', 'Perbaikan Sistem'] as $opt)
                    <span class="cb-item">
                        <span class="cb-box">{!! in_array($opt, $jenis_perubahan) ? 'X' : '&nbsp;' !!}</span>{{ $opt }}
                    </span>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="w-col1" style="vertical-align: top; padding-top: 6px;">Jenis Permohonan</td>
            <td class="w-col2">
                <table class="inner-table">
                    <tr>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('I. Permintaan Sub-Domain', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>I. Permintaan Sub-Domain</span>
                        </td>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('II. Perbaikan Sistem', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>II. Perbaikan Sistem</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('III. Pembuatan Akun Mail', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>III. Pembuatan Akun Mail</span>
                        </td>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('IV. Reset Password Mail', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>IV. Reset Password Mail</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('V. Penghapusan Akun Mail', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>V. Penghapusan Akun Mail</span>
                        </td>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('VI. Akun Repository', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>VI. Akun Repository</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('VII. SSL', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>VII. SSL</span>
                        </td>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('VIII. Deaktivasi Aplikasi', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>VIII. Deaktivasi Aplikasi</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('IX. Repointing', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>IX. Repointing</span>
                        </td>
                        <td>
                            <span class="cb-item"><span class="cb-box">{!! in_array('X. Replikasi Aplikasi', $jenis_permohonan) ? 'X' : '&nbsp;' !!}</span>X. Replikasi Aplikasi</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="w-col1">Nama Aplikasi</td>
            <td class="w-col2">{{ $data->nama_aplikasi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Deskripsi Aplikasi</td>
            <td class="w-col2">{{ $data->deskripsi_aplikasi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Alamat Aplikasi</td>
            <td class="w-col2">{{ $data->alamat_aplikasi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Alamat Repository</td>
            <td class="w-col2">{{ $data->alamat_repository ?? '-' }}</td>
        </tr>

        <tr>
            <td colspan="2" class="section-title">Perubahan yang Diharapkan</td>
        </tr>
        <tr>
            <td class="w-col1">Latar belakang perubahan</td>
            <td class="w-col2">{{ $data->latar_belakang ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Rincian atas perubahan yang diajukan</td>
            <td class="w-col2">{{ $data->rincian_perubahan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1" style="vertical-align: top; padding-top: 6px;">
                Risiko terkait bila perubahan tidak dilakukan<br><br>
            </td>
            <td class="w-col2" style="vertical-align: top;">
                <span style="font-weight: bold; font-size: 12px; margin-bottom: 6px;">Kriteria:</span>
                <div style="margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px dashed #ccc;">
                    @foreach(['Malapetaka', 'Sangat Berat', 'Berat', 'Agak Berat', 'Tidak Berat'] as $opt)
                        <span class="cb-item">
                            <span class="cb-box">{!! in_array($opt, $kriteria_risiko) ? 'X' : '&nbsp;' !!}</span>{{ $opt }}
                        </span>
                    @endforeach
                </div>
                <div style="margin-top: 4px; border-top: 1px dashed #bbb; padding-top: 4px;">
                    <strong style="font-size: 12px;">Keterangan:</strong>
                    <div style="margin-top: 2px; padding-left: 2px;">
                        {{ $data->risiko_tidak_dilakukan ?? '-' }}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="w-col1">Solusi yang diharapkan</td>
            <td class="w-col2">{{ $data->solusi_diharapkan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Risiko Perubahan</td>
            <td class="w-col2">{{ $data->risiko_perubahan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Alternatif Perubahan</td>
            <td class="w-col2">{{ $data->alternatif_perubahan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Biaya Perubahan</td>
            <td class="w-col2">{{ $data->biaya_perubahan ?? '0' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Waktu Perubahan</td>
            <td class="w-col2">{{ $data->waktu_perubahan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Lampiran</td>
            <td class="w-col2">{{ $data->lampiran ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-col1">Tanggal Permohonan</td>
            <td class="w-col2">{{ $data->tanggal_permohonan ? \Carbon\Carbon::parse($data->tanggal_permohonan)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>

    <table class="ttd-table">
        <tr>
            <td>
                <div>Tanggal: </div>
                <div style="margin-top: 3px;">Dibuat Oleh: </div>
                <div style="margin-top: 3px;">Pemohon,</div>
                <div class="ttd-space"></div>
                <div>Nama: {{ $data->pemohon }}</div>
                <div>Jabatan: </div>
            </td>
            <td>
                <div>Tanggal: </div>
                <div style="margin-top: 3px;">Diterima Oleh: </div>
                <div style="margin-top: 3px;">Koordinator Agen,</div>
                <div class="ttd-space"></div>
                <div>Nama: </div>
                <div>Jabatan: </div>
            </td>
        </tr>
        <tr>
            <td>
                <div>Tanggal: </div>
                <div style="margin-top: 3px;">Disetujui Oleh: </div>
                <div style="margin-top: 3px;">Penanggung Jawab Perangkat,</div>
                <div class="ttd-space"></div>
                <div>Nama: </div>
                <div>Jabatan: </div>
            </td>
            <td>
                <div>Tanggal: </div>
                <div style="margin-top: 3px;">Dilaksanakan Oleh: </div>
                <div style="margin-top: 3px;">Agen,</div>
                <div class="ttd-space"></div>
                <div>Nama: </div>
                <div>Jabatan: </div>
            </td>
        </tr>
    </table>

</body>
</html>