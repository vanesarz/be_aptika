<?php

namespace App\Http\Controllers;

use App\Models\Spd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpdController extends Controller
{
    public function index()
    {
        $spds = Spd::orderBy('created_at', 'desc')->get();
        
        $mapped = $spds->map(function($spd) {
            return [
                'id' => $spd->id,
                'noSpd' => $spd->no_spd,
                'nama' => $spd->nama,
                'nip' => $spd->nip,
                'tujuan' => $spd->tempat_tujuan,
                'maksud' => $spd->maksud,
                'tglMulai' => $spd->tgl_mulai ? $spd->tgl_mulai->format('Y-m-d') : null,
                'tglSelesai' => $spd->tgl_selesai ? $spd->tgl_selesai->format('Y-m-d') : null,
                'status' => $spd->status,
                'anggaran' => $spd->anggaran,
                'pejabatPemberi' => $spd->pejabat_pemberi,
                'pangkat' => $spd->pangkat,
                'jabatan' => $spd->jabatan,
                'angkutan' => $spd->angkutan,
                'tempatBerangkat' => $spd->tempat_berangkat,
                'tempatTujuan' => $spd->tempat_tujuan,
                'durasi' => $spd->durasi,
                'pengikut' => $spd->pengikut,
            ];
        });

        return response()->json($mapped);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'nip' => 'required|string',
            'tempatTujuan' => 'required|string',
            'tglMulai' => 'required|date',
            'tglSelesai' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $spd = Spd::create([
            'no_spd' => $request->noSpd,
            'pejabat_pemberi' => $request->pejabatPemberi,
            'nama' => $request->nama,
            'nip' => $request->nip,
            'pangkat' => $request->pangkat,
            'jabatan' => $request->jabatan,
            'maksud' => $request->maksud,
            'angkutan' => $request->angkutan,
            'tempat_berangkat' => $request->tempatBerangkat,
            'tempat_tujuan' => $request->tempatTujuan,
            'tgl_mulai' => $request->tglMulai,
            'tgl_selesai' => $request->tglSelesai,
            'durasi' => $request->durasi,
            'pengikut' => $request->pengikut,
            'anggaran' => $request->anggaran,
            'status' => $request->status ?? 'DRAF',
        ]);

        return response()->json(['message' => 'Spd created successfully', 'data' => $spd], 201);
    }

    public function show($id)
    {
        $spd = Spd::find($id);
        if (!$spd) {
            return response()->json(['message' => 'Spd not found'], 404);
        }

        $mapped = [
            'id' => $spd->id,
            'noSpd' => $spd->no_spd,
            'nama' => $spd->nama,
            'nip' => $spd->nip,
            'tujuan' => $spd->tempat_tujuan,
            'maksud' => $spd->maksud,
            'tglMulai' => $spd->tgl_mulai ? $spd->tgl_mulai->format('Y-m-d') : null,
            'tglSelesai' => $spd->tgl_selesai ? $spd->tgl_selesai->format('Y-m-d') : null,
            'status' => $spd->status,
            'anggaran' => $spd->anggaran,
            'pejabatPemberi' => $spd->pejabat_pemberi,
            'pangkat' => $spd->pangkat,
            'jabatan' => $spd->jabatan,
            'angkutan' => $spd->angkutan,
            'tempatBerangkat' => $spd->tempat_berangkat,
            'tempatTujuan' => $spd->tempat_tujuan,
            'durasi' => $spd->durasi,
            'pengikut' => $spd->pengikut,
        ];

        return response()->json($mapped);
    }

    public function update(Request $request, $id)
    {
        $spd = Spd::find($id);
        if (!$spd) {
            return response()->json(['message' => 'Spd not found'], 404);
        }

        $spd->update([
            'no_spd' => $request->noSpd ?? $spd->no_spd,
            'pejabat_pemberi' => $request->pejabatPemberi ?? $spd->pejabat_pemberi,
            'nama' => $request->nama ?? $spd->nama,
            'nip' => $request->nip ?? $spd->nip,
            'pangkat' => $request->pangkat ?? $spd->pangkat,
            'jabatan' => $request->jabatan ?? $spd->jabatan,
            'maksud' => $request->maksud ?? $spd->maksud,
            'angkutan' => $request->angkutan ?? $spd->angkutan,
            'tempat_berangkat' => $request->tempatBerangkat ?? $spd->tempat_berangkat,
            'tempat_tujuan' => $request->tempatTujuan ?? $spd->tempat_tujuan,
            'tgl_mulai' => $request->tglMulai ?? $spd->tgl_mulai,
            'tgl_selesai' => $request->tglSelesai ?? $spd->tgl_selesai,
            'durasi' => $request->durasi ?? $spd->durasi,
            'pengikut' => $request->pengikut ?? $spd->pengikut,
            'anggaran' => $request->anggaran ?? $spd->anggaran,
            'status' => $request->status ?? $spd->status,
        ]);

        return response()->json(['message' => 'Spd updated successfully', 'data' => $spd]);
    }

    public function destroy($id)
    {
        $spd = Spd::find($id);
        if (!$spd) {
            return response()->json(['message' => 'Spd not found'], 404);
        }

        $spd->delete();

        return response()->json(['message' => 'Spd deleted successfully']);
    }

    public function submitLaporan(Request $request)
    {
        return response()->json(['message' => 'Laporan submitted']);
    }
}
