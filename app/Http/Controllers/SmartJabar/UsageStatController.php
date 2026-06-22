<?php

namespace App\Http\Controllers\SmartJabar;

use App\Http\Controllers\Controller;
use App\Models\GeneralOpd;
use App\Models\SmartjabarUsageStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Rules\UniquePeriod;

class UsageStatController extends Controller
{
    protected $smartJabarId = 2;

    private function getOpdList()
    {
        return [
            "BADAN PENGHUBUNG",
            "SEKRETARIAT DPRD",
            "DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL",
            "DINAS PEMUDA DAN OLAHRAGA",
            "DINAS PEMBERDAYAAN MASYARAKAT DAN DESA",
            "BADAN PENANGGULANGAN BENCANA DAERAH",
            "INSPEKTORAT DAERAH",
            "BADAN PENGEMBANGAN SUMBER DAYA MANUSIA",
            "SATUAN POLISI PAMONG PRAJA",
            "DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU",
            "BADAN KESATUAN BANGSA DAN POLITIK",
            "BADAN PENELITIAN DAN PENGEMBANGAN DAERAH",
            "BADAN PERENCANAAN PEMBANGUNAN DAERAH",
            "DINAS KOMUNIKASI DAN INFORMATIKA",
            "DINAS PARIWISATA DAN KEBUDAYAAN",
            "DINAS SOSIAL",
            "BADAN KEPEGAWAIAN DAERAH",
            "DINAS KEHUTANAN",
            "DINAS PERINDUSTRIAN DAN PERDAGANGAN",
            "DINAS PERPUSTAKAAN DAN KEARSIPAN DAERAH",
            "DINAS KELAUTAN DAN PERIKANAN",
            "DINAS PEMBERDAYAAN PEREMPUAN, PERLINDUNGAN ANAK, DAN KELUARGA BERENCANA",
            "DINAS PERKEBUNAN",
            "DINAS KOPERASI DAN USAHA KECIL",
            "DINAS KETAHANAN PANGAN DAN PETERNAKAN",
            "DINAS PERHUBUNGAN",
            "DINAS SUMBER DAYA AIR",
            "DINAS BINA MARGA DAN PENATAAN RUANG",
            "DINAS TANAMAN PANGAN DAN HORTIKULTURA",
            "DINAS LINGKUNGAN HIDUP",
            "DINAS PERUMAHAN DAN PERMUKIMAN",
            "BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH",
            "DINAS TENAGA KERJA DAN TRANSMIGRASI",
            "DINAS ENERGI DAN SUMBER DAYA MINERAL",
            "BADAN PENDAPATAN DAERAH",
            "SEKRETARIAT DAERAH",
            "DINAS KESEHATAN",
            "DINAS PENDIDIKAN"
        ];
    }

    public function index(Request $request)
    {
        $query = SmartjabarUsageStat::where('service_type_id', $this->smartJabarId);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->orderByDesc('year')->orderByDesc('month')->get();

        return response()->json(compact('data'));
    }

    public function create()
    {
        $opds = $this->getOpdList();
        return response()->json(compact('opds'));
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'opd_id'       => 'required|exists:general_opd,id',
        'month'        => [
            'required',
            'integer',
            'min:1',
            'max:12',
            new UniquePeriod('smartjabar_usage_stats', $request->year, null, 'opd_id', $request->opd_id)
        ],
        'year'         => 'required|integer|min:2000|max:2099',
        'total_asn'    => 'required|integer|min:0',
        'active_users' => 'required|integer|min:0|lte:total_asn',
    ]);

    $stat = SmartjabarUsageStat::create([
        'service_type_id' => $this->smartJabarId,
        'opd_id'          => $validated['opd_id'],
        'month'           => $validated['month'],
        'year'            => $validated['year'],
        'total_asn'       => $validated['total_asn'],
        'active_users'    => $validated['active_users'],
    ]);

    return response()->json([
        'message' => 'Data statistik berhasil disimpan.',
    ], 201);
}
    public function edit($id)
    {
        $stat = SmartjabarUsageStat::findOrFail($id);
        $opds = $this->getOpdList();
        return response()->json(compact('stat', 'opds'));
    }

    public function update(Request $request, $id)
    {
        $stat = SmartjabarUsageStat::findOrFail($id);

        $validated = $request->validate([
            'opd_id'       => 'required',
            'month'        => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('smartjabar_usage_stats', $request->year, $id, 'opd_id', $request->opd_id)
            ],
            'year'         => 'required|integer|min:2000|max:2099',
            'total_asn'    => 'required|integer|min:0',
            'active_users' => 'required|integer|min:0|lte:total_asn',
        ]);

        $stat->update($validated);

        return response()->json(['message' => 'Statistik berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        SmartjabarUsageStat::findOrFail($id)->delete();
        return response()->json(['message' => 'Statistik berhasil dihapus.']);
    }
}
