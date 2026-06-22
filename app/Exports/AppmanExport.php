<?php

namespace App\Exports;

use App\Models\AppmanInventoryStat;
use App\Models\AppmanTeamSupportFacility;
use App\Models\AppmanIntegrationMapping;
use App\Models\AppmanDevelopmentTarget;
use App\Models\AppmanAppVulnerability;
use App\Models\AppmanKatalapsRegency;
use App\Models\AppmanEmailManagementStat;
use App\Models\AppmanDriveJabarStat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class AppmanExport
{
    protected $year;
    protected $month;

    // Warna header utama (kuning keemasan seperti contoh)
    const COLOR_HEADER = 'FFC000';
    // Warna header section (biru muda)
    const COLOR_SUB    = 'BDD7EE';

    public function __construct($year, $month)
    {
        $this->year  = $year;
        $this->month = $month;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap');

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        // =========================================================
        // AMBIL DATA DARI DATABASE
        // =========================================================

        $months = ['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGS','SEP','OKT','NOV','DES'];

        // Helper: buat array bulan 1–12 berisi 0
        $blank = fn() => array_fill(1, 12, 0);

        // --- 1. PENDATAAN APLIKASI (INVENTARIS) ---
        $invTotalApps   = $blank();
        $invProfile     = $blank();
        $invRepository  = $blank();
        $invRegistered  = $blank();

        AppmanInventoryStat::where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->each(function ($r) use (
                &$invTotalApps, &$invProfile,
                &$invRepository, &$invRegistered
            ) {
                $invTotalApps[$r->month]  += $r->total_apps;
                $invProfile[$r->month]    += $r->profile;
                $invRepository[$r->month] += $r->repository;
                $invRegistered[$r->month] += $r->registered_pse;
            });

        // --- 2. FASILITASI DUKUNGAN TIM (TOT) ---
        $totPd    = $blank();
        $totApps  = $blank();
        $totTotal = $blank();

        AppmanTeamSupportFacility::where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->each(function ($r) use (&$totPd, &$totApps, &$totTotal) {
                $totPd[$r->month]    += $r->total_pd;
                $totApps[$r->month]  += $r->total_apps;
                $totTotal[$r->month] += ($r->total_pd + $r->total_apps);
            });

        // --- 3. PEMETAAN INTEGRASI APLIKASI ---
        $intTotalApps    = $blank();
        $intOpportunity  = $blank();
        $intIntegrated   = $blank();
        $intNotIntegrated = $blank();

        AppmanIntegrationMapping::where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->each(function ($r) use (
                &$intTotalApps, &$intOpportunity,
                &$intIntegrated, &$intNotIntegrated
            ) {
                $intTotalApps[$r->month]    += $r->total_apps;
                $intOpportunity[$r->month]  += $r->integration_opportunity;
                $intIntegrated[$r->month]   += $r->integrated;
                $intNotIntegrated[$r->month] += ($r->integration_opportunity - $r->integrated);
            });

        // --- 4. TARGET PENGEMBANGAN APLIKASI ---
        $devOutsideDC   = $blank();
        $devManualSvc   = $blank();

        AppmanDevelopmentTarget::where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->each(function ($r) use (&$devOutsideDC, &$devManualSvc) {
                $devOutsideDC[$r->month]  += $r->outside_dc_jabar;
                $devManualSvc[$r->month]  += $r->manual_service;
            });

        // --- 5. KERENTANAN APLIKASI ---
        $vulnTotal = $blank();

        AppmanAppVulnerability::where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->each(function ($r) use (&$vulnTotal) {
                $vulnTotal[$r->month] += $r->total_apps;
            });

        // --- 6. KATALAPS KABUPATEN KOTA ---
        $katalapsData = AppmanKatalapsRegency::with('regency')
            ->where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->groupBy(fn($r) => $r->regency->name ?? 'Unknown');

        // --- 7. EMAIL MANAGEMENT ---
        $emailAsn    = $blank();
        $emailOthers = $blank();
        $emailActive = $blank();

        AppmanEmailManagementStat::where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->each(function ($r) use (&$emailAsn, &$emailOthers, &$emailActive) {
                $emailAsn[$r->month]    += $r->user_asn;
                $emailOthers[$r->month] += $r->user_others;
                $emailActive[$r->month] += $r->active_user;
            });

        // --- 8. DRIVE JABAR ---
        $driveUsers = $blank();

        AppmanDriveJabarStat::where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->each(function ($r) use (&$driveUsers) {
                $driveUsers[$r->month] += $r->total_users;
            });

        // =========================================================
        // HEADER BARIS 1 – JUDUL UTAMA
        // =========================================================

        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'TIM PENGELOLAAN APLIKASI');
        $this->applyMainTitle($sheet, 'A1:N1');

        // =========================================================
        // SECTION 1 – PENDATAAN APLIKASI (KATALAPS)
        // Kolom A–N (1–14)
        // =========================================================

        $this->writeSectionHeader($sheet, 'A2:N2', 'PENDATAAN APLIKASI ' . $this->year . ' (BERDASARKAN KATALAPS)');

        // Sub-header: tahun di A3, bulan B3:M3
        $sheet->setCellValue('A3', $this->year);
        $this->applySubHeader($sheet, 'A3');
        $this->writeMonthHeaders($sheet, 'B3', $months);

        // Baris data
        $sheet->setCellValue('A4', 'JUMLAH APLIKASI');
        $sheet->setCellValue('A5', 'PROFIL');
        $sheet->setCellValue('A6', 'REPOSITORY');
        $sheet->setCellValue('A7', 'TERDAFTAR PSE');

        $this->fillRowData($sheet, 'B4', $invTotalApps);
        $this->fillRowData($sheet, 'B5', $invProfile);
        $this->fillRowData($sheet, 'B6', $invRepository);
        $this->fillRowData($sheet, 'B7', $invRegistered);

        $this->applyBorder($sheet, 'A2:N7');

        // =========================================================
        // SECTION 2 – FASILITASI DUKUNGAN TIM (TOT)
        // Kolom P–AC (16–29) → mulai kolom 16
        // =========================================================

        $colTot = 'P'; // kolom 16

        $this->writeSectionHeader(
            $sheet,
            $colTot . '2:' . $this->colOffset($colTot, 13) . '2',
            'FASILITASI DUKUNGAN TIM PADA PENGEMBANGAN APLIKASI PERANGKAT DAERAH (TOT)'
        );

        $sheet->setCellValue($colTot . '3', $this->year);
        $this->applySubHeader($sheet, $colTot . '3');
        $this->writeMonthHeaders($sheet, $this->colOffset($colTot, 1) . '3', $months);

        $sheet->setCellValue($colTot . '4', 'JUMLAH PD');
        $sheet->setCellValue($colTot . '5', 'JUMLAH APLIKASI');
        $sheet->setCellValue($colTot . '6', 'TOTAL');

        $this->fillRowData($sheet, $this->colOffset($colTot, 1) . '4', $totPd);
        $this->fillRowData($sheet, $this->colOffset($colTot, 1) . '5', $totApps);
        $this->fillRowData($sheet, $this->colOffset($colTot, 1) . '6', $totTotal);

        $this->applyBorder($sheet, $colTot . '2:' . $this->colOffset($colTot, 13) . '6');

        // =========================================================
        // SECTION 3 – PEMETAAN INTEGRASI APLIKASI
        // Kolom AE–AR (31–44)
        // =========================================================

        $colInt = $this->colOffset('A', 30); // AE

        $this->writeSectionHeader(
            $sheet,
            $colInt . '2:' . $this->colOffset($colInt, 13) . '2',
            'PEMETAAN INTEGRASI APLIKASI'
        );

        $sheet->setCellValue($colInt . '3', $this->year);
        $this->applySubHeader($sheet, $colInt . '3');
        $this->writeMonthHeaders($sheet, $this->colOffset($colInt, 1) . '3', $months);

        $sheet->setCellValue($colInt . '4', 'JUMLAH APLIKASI');
        $sheet->setCellValue($colInt . '5', 'PELUANG INTEGRASI');
        $sheet->setCellValue($colInt . '6', 'SUDAH INTEGRASI');
        $sheet->setCellValue($colInt . '7', 'BELUM INTEGRASI');

        $this->fillRowData($sheet, $this->colOffset($colInt, 1) . '4', $intTotalApps);
        $this->fillRowData($sheet, $this->colOffset($colInt, 1) . '5', $intOpportunity);
        $this->fillRowData($sheet, $this->colOffset($colInt, 1) . '6', $intIntegrated);
        $this->fillRowData($sheet, $this->colOffset($colInt, 1) . '7', $intNotIntegrated);

        $this->applyBorder($sheet, $colInt . '2:' . $this->colOffset($colInt, 13) . '7');

        // =========================================================
        // SECTION 4 – TARGET PENGEMBANGAN APLIKASI
        // Kolom AT–BG (46–59)
        // =========================================================

        $colDev = $this->colOffset('A', 45); // AT

        $this->writeSectionHeader(
            $sheet,
            $colDev . '2:' . $this->colOffset($colDev, 13) . '2',
            'APLIKASI/LAYANAN YANG MENJADI TARGET PENGEMBANGAN'
        );

        $sheet->setCellValue($colDev . '3', $this->year);
        $this->applySubHeader($sheet, $colDev . '3');
        $this->writeMonthHeaders($sheet, $this->colOffset($colDev, 1) . '3', $months);

        $sheet->setCellValue($colDev . '4', 'LUAR DC JABAR');
        $sheet->setCellValue($colDev . '5', 'LAYANAN MANUAL');

        $this->fillRowData($sheet, $this->colOffset($colDev, 1) . '4', $devOutsideDC);
        $this->fillRowData($sheet, $this->colOffset($colDev, 1) . '5', $devManualSvc);

        $this->applyBorder($sheet, $colDev . '2:' . $this->colOffset($colDev, 13) . '5');

        // =========================================================
        // SECTION 5 – KERENTANAN APLIKASI
        // Kolom BI–BW (61–75)
        // =========================================================

        $colVuln = $this->colOffset('A', 60); // BI

        $this->writeSectionHeader(
            $sheet,
            $colVuln . '2:' . $this->colOffset($colVuln, 14) . '2',
            'KERENTAAN PADA APLIKASI PEMPROV JABAR'
        );

        $sheet->setCellValue($colVuln . '3', $this->year);
        $this->applySubHeader($sheet, $colVuln . '3');
        $this->writeMonthHeaders($sheet, $this->colOffset($colVuln, 1) . '3', $months);

        $sheet->setCellValue($colVuln . '4', 'JUMLAH APLIKASI');
        $this->fillRowData($sheet, $this->colOffset($colVuln, 1) . '4', $vulnTotal);

        $this->applyBorder($sheet, $colVuln . '2:' . $this->colOffset($colVuln, 14) . '4');

        // =========================================================
        // SECTION 6 – KATALAPS KABUPATEN KOTA
        // Kolom BY–CL (77–90)
        // =========================================================

        $colKab = $this->colOffset('A', 76); // BY

        $this->writeSectionHeader(
            $sheet,
            $colKab . '2:' . $this->colOffset($colKab, 14) . '2',
            'KATALAPS KABUPATEN KOTA'
        );

        // Header sub
        $sheet->setCellValue($colKab . '3', 'No');
        $sheet->setCellValue($this->colOffset($colKab, 1) . '3', 'KABUPATEN/KOTA');
        $this->applySubHeader($sheet, $colKab . '3');
        $this->applySubHeader($sheet, $this->colOffset($colKab, 1) . '3');
        $this->writeMonthHeaders($sheet, $this->colOffset($colKab, 2) . '3', $months);

        $rowKab = 4;
        $noKab  = 1;

        foreach ($katalapsData as $regencyName => $records) {
            $sheet->setCellValue($colKab . $rowKab, $noKab);
            $sheet->setCellValue($this->colOffset($colKab, 1) . $rowKab, $regencyName);

            $appByMonth = $blank();
            foreach ($records as $r) {
                $appByMonth[$r->month] += $r->app_count;
            }

            $this->fillRowData($sheet, $this->colOffset($colKab, 2) . $rowKab, $appByMonth);

            $rowKab++;
            $noKab++;
        }

        if ($rowKab > 4) {
            $this->applyBorder($sheet, $colKab . '2:' . $this->colOffset($colKab, 14) . ($rowKab - 1));
        }

        // =========================================================
        // SECTION 7 – LAYANAN PENGELOLAAN EMAIL
        // Kolom CN–DB (92–106)
        // =========================================================

        $colEmail = $this->colOffset('A', 91); // CN

        $this->writeSectionHeader(
            $sheet,
            $colEmail . '2:' . $this->colOffset($colEmail, 14) . '2',
            'LAYANAN PENGELOLAAN EMAIL'
        );

        $sheet->setCellValue($colEmail . '3', $this->year);
        $this->applySubHeader($sheet, $colEmail . '3');
        $this->writeMonthHeaders($sheet, $this->colOffset($colEmail, 1) . '3', $months);

        $sheet->setCellValue($colEmail . '4', 'JUMLAH USER (ASN)');
        $sheet->setCellValue($colEmail . '5', 'JUMLAH USER (LAINNYA)');
        $sheet->setCellValue($colEmail . '6', 'JUMLAH USER AKTIF');

        $this->fillRowData($sheet, $this->colOffset($colEmail, 1) . '4', $emailAsn);
        $this->fillRowData($sheet, $this->colOffset($colEmail, 1) . '5', $emailOthers);
        $this->fillRowData($sheet, $this->colOffset($colEmail, 1) . '6', $emailActive);

        $this->applyBorder($sheet, $colEmail . '2:' . $this->colOffset($colEmail, 14) . '6');

        // =========================================================
        // SECTION 8 – LAYANAN DRIVE JABAR
        // Kolom DD–DR (108–122)
        // =========================================================

        $colDrive = $this->colOffset('A', 107); // DD

        $this->writeSectionHeader(
            $sheet,
            $colDrive . '2:' . $this->colOffset($colDrive, 13) . '2',
            'LAYANAN DRIVE JABAR'
        );

        $sheet->setCellValue($colDrive . '3', $this->year);
        $this->applySubHeader($sheet, $colDrive . '3');
        $this->writeMonthHeaders($sheet, $this->colOffset($colDrive, 1) . '3', $months);

        $sheet->setCellValue($colDrive . '4', 'JUMLAH USER');
        $this->fillRowData($sheet, $this->colOffset($colDrive, 1) . '4', $driveUsers);

        $this->applyBorder($sheet, $colDrive . '2:' . $this->colOffset($colDrive, 13) . '4');

        // =========================================================
        // CHARTS – satu chart per section, di bawah tabel masing-masing
        // Tiap chart menempati ~13 baris di bawah tabel (baris 9–21)
        // =========================================================

        // CHART 1 – Pendataan Aplikasi (Katalaps)
        // Data tabel: baris 3–7, kolom A–N  → chart mulai baris 9
        $this->createChart(
            $sheet,
            'chartInventory',
            'PENDATAAN APLIKASI',
            'Rekap!$B$3:$M$3',   // kategori (bulan)
            'Rekap!$B$4:$M$7',   // nilai (4 baris: jumlah, profil, repo, pse)
            4,
            'A9', 'N21'
        );

        // CHART 2 – Fasilitasi Dukungan Tim (TOT)
        // Tabel: baris 3–6, kolom P–AC
        $colTotStart = $this->colOffset('P', 1);   // Q
        $colTotEnd   = $this->colOffset('P', 12);  // AB
        $this->createChart(
            $sheet,
            'chartTot',
            'FASILITASI DUKUNGAN TIM (TOT)',
            'Rekap!$' . $colTotStart . '$3:$' . $colTotEnd . '$3',
            'Rekap!$' . $colTotStart . '$4:$' . $colTotEnd . '$6',
            3,
            'P9', $this->colOffset('P', 13) . '21'
        );

        // CHART 3 – Pemetaan Integrasi
        // Tabel: baris 3–7, kolom AE–AR
        $colInt      = $this->colOffset('A', 30);  // AE
        $colIntStart = $this->colOffset($colInt, 1);
        $colIntEnd   = $this->colOffset($colInt, 12);
        $this->createChart(
            $sheet,
            'chartIntegration',
            'PEMETAAN INTEGRASI APLIKASI',
            'Rekap!$' . $colIntStart . '$3:$' . $colIntEnd . '$3',
            'Rekap!$' . $colIntStart . '$4:$' . $colIntEnd . '$7',
            4,
            $colInt . '9', $this->colOffset($colInt, 13) . '21'
        );

        // CHART 4 – Target Pengembangan Aplikasi
        // Tabel: baris 3–5, kolom AT–BG
        $colDev      = $this->colOffset('A', 45);  // AT
        $colDevStart = $this->colOffset($colDev, 1);
        $colDevEnd   = $this->colOffset($colDev, 12);
        $this->createChart(
            $sheet,
            'chartDev',
            'TARGET PENGEMBANGAN APLIKASI',
            'Rekap!$' . $colDevStart . '$3:$' . $colDevEnd . '$3',
            'Rekap!$' . $colDevStart . '$4:$' . $colDevEnd . '$5',
            2,
            $colDev . '9', $this->colOffset($colDev, 13) . '21'
        );

        // CHART 5 – Kerentanan Aplikasi
        // Tabel: baris 3–4, kolom BI–BW
        $colVuln      = $this->colOffset('A', 60);  // BI
        $colVulnStart = $this->colOffset($colVuln, 1);
        $colVulnEnd   = $this->colOffset($colVuln, 12);
        $this->createChart(
            $sheet,
            'chartVuln',
            'KERENTANAN APLIKASI',
            'Rekap!$' . $colVulnStart . '$3:$' . $colVulnEnd . '$3',
            'Rekap!$' . $colVulnStart . '$4:$' . $colVulnEnd . '$4',
            1,
            $colVuln . '9', $this->colOffset($colVuln, 14) . '21'
        );

        // CHART 6 – Layanan Email
        // Tabel: baris 3–6, kolom CN–DB
        $colEmail      = $this->colOffset('A', 91);  // CN
        $colEmailStart = $this->colOffset($colEmail, 1);
        $colEmailEnd   = $this->colOffset($colEmail, 12);
        $this->createChart(
            $sheet,
            'chartEmail',
            'LAYANAN PENGELOLAAN EMAIL',
            'Rekap!$' . $colEmailStart . '$3:$' . $colEmailEnd . '$3',
            'Rekap!$' . $colEmailStart . '$4:$' . $colEmailEnd . '$6',
            3,
            $colEmail . '9', $this->colOffset($colEmail, 14) . '21'
        );

        // CHART 7 – Drive Jabar
        // Tabel: baris 3–4, kolom DD–DR
        $colDrive      = $this->colOffset('A', 107); // DD
        $colDriveStart = $this->colOffset($colDrive, 1);
        $colDriveEnd   = $this->colOffset($colDrive, 12);
        $this->createChart(
            $sheet,
            'chartDrive',
            'LAYANAN DRIVE JABAR',
            'Rekap!$' . $colDriveStart . '$3:$' . $colDriveEnd . '$3',
            'Rekap!$' . $colDriveStart . '$4:$' . $colDriveEnd . '$4',
            1,
            $colDrive . '9', $this->colOffset($colDrive, 13) . '21'
        );

        // =========================================================
        // AUTO SIZE semua kolom
        // =========================================================

        $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($colDrive) + 13;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

        for ($i = 1; $i <= $lastColIndex; $i++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // =========================================================
        // SIMPAN FILE
        // =========================================================

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $path   = storage_path('app/public/appman.xlsx');
        $writer->save($path);

        return $path;
    }

    // =========================================================
    // HELPER METHODS
    // =========================================================

    /**
     * Judul utama sheet (baris 1)
     */
    private function applyMainTitle($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::COLOR_HEADER],
            ],
        ]);
    }

    /**
     * Header section (judul tiap blok tabel)
     */
    private function writeSectionHeader($sheet, $range, $title)
    {
        [$startCell] = explode(':', $range);
        $sheet->mergeCells($range);
        $sheet->setCellValue($startCell, $title);
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText'   => true,
            ],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::COLOR_HEADER],
            ],
        ]);
    }

    /**
     * Style sub-header (tahun / kolom label)
     */
    private function applySubHeader($sheet, $cell)
    {
        $sheet->getStyle($cell)->applyFromArray([
            'font'      => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::COLOR_SUB],
            ],
        ]);
    }

    /**
     * Tulis header bulan (JAN–DES) mulai dari $startCell ke kanan
     */
    private function writeMonthHeaders($sheet, $startCell, array $months)
    {
        preg_match('/([A-Z]+)(\d+)/', $startCell, $m);
        $col = $m[1];
        $row = $m[2];

        foreach ($months as $label) {
            $sheet->setCellValue($col . $row, $label);
            $this->applySubHeader($sheet, $col . $row);
            $col = $this->nextCol($col);
        }
    }

    /**
     * Isi data array bulan (1–12) ke baris mulai $startCell
     */
    private function fillRowData($sheet, $startCell, array $data)
    {
        preg_match('/([A-Z]+)(\d+)/', $startCell, $m);
        $col = $m[1];
        $row = $m[2];

        for ($i = 1; $i <= 12; $i++) {
            $sheet->setCellValue($col . $row, $data[$i] ?? 0);
            $sheet->getStyle($col . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col = $this->nextCol($col);
        }
    }

    /**
     * Terapkan border tipis pada range
     */
    private function applyBorder($sheet, $range)
    {
        $sheet->getStyle($range)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Buat bar chart dan tambahkan ke sheet.
     *
     * @param $sheet         Worksheet aktif
     * @param $name          ID unik chart
     * @param $title         Judul chart
     * @param $categoryRange Range string kategori, misal 'Rekap!$B$3:$M$3'
     * @param $valueRange    Range string nilai, misal 'Rekap!$B$4:$M$6'
     * @param $seriesCount   Jumlah baris/series dalam valueRange
     * @param $topLeft       Cell pojok kiri atas chart
     * @param $bottomRight   Cell pojok kanan bawah chart
     */
    private function createChart(
        $sheet,
        string $name,
        string $titleText,
        string $categoryRange,
        string $valueRange,
        int $seriesCount,
        string $topLeft,
        string $bottomRight
    ) {
        $categories = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $categoryRange,
                null,
                12
            ),
        ];

        // Pisahkan valueRange menjadi per-baris series
        // Format range: 'Rekap!$B$4:$M$6'  → baris 4, 5, 6
        preg_match('/\$([A-Z]+)\$(\d+):\$([A-Z]+)\$(\d+)/', $valueRange, $m);
        $startCol  = $m[1];
        $startRow  = (int) $m[2];
        $endCol    = $m[3];

        $values = [];
        for ($i = 0; $i < $seriesCount; $i++) {
            $row      = $startRow + $i;
            $rangeRow = 'Rekap!$' . $startCol . '$' . $row . ':$' . $endCol . '$' . $row;
            $values[] = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $rangeRow,
                null,
                12
            );
        }

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values) - 1),
            [],
            $categories,
            $values
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$series]);
        $legend   = new Legend(Legend::POSITION_RIGHT, null, false);
        $chartObj = new Chart(
            $name,
            new Title($titleText),
            $legend,
            $plotArea
        );

        $chartObj->setTopLeftPosition($topLeft);
        $chartObj->setBottomRightPosition($bottomRight);
        $sheet->addChart($chartObj);
    }

    /**
     * Kolom berikutnya (misal: A → B, Z → AA)
     */
    private function nextCol(string $col): string
    {
        $index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col);
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
    }

    /**
     * Kolom dengan offset n dari $col
     */
    private function colOffset(string $col, int $offset): string
    {
        $index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col);
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + $offset);
    }
}