<?php

namespace App\Exports;

use App\Models\RekayasaApplicationReplication;
use App\Models\RekayasaMentoringPerformance;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\{
    Chart,
    DataSeries,
    DataSeriesValues,
    Legend,
    PlotArea,
    Title
};
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekayasaApplicationExport
{
    protected $year;

    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;

        $this->month = $month;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Rekap');

        // =================================================
        // LANDSCAPE
        // =================================================

        $sheet->getPageSetup()->setOrientation(
            PageSetup::ORIENTATION_LANDSCAPE
        );

        // =================================================
        // HEADER BULAN
        // =================================================

        $headers = [
            $this->year,
            'JAN',
            'FEB',
            'MAR',
            'APR',
            'MEI',
            'JUN',
            'JUL',
            'AGS',
            'SEP',
            'OKT',
            'NOV',
            'DES'
        ];

        // =================================================
        // DATA REPLIKASI
        // =================================================

        $records = RekayasaApplicationReplication::with(
            'institutionCategory'
        )
        ->where('year', $this->year)
        ->where('month', '<=', $this->month)
        ->get();

        // =================================================
        // TABLE 1 DATA
        // =================================================

        $data1 = array_fill(1, 12, 0);

        foreach ($records as $row) {

            $data1[$row->month] +=
                $row->total_replications;
        }

        // =================================================
        // TABLE 2 DATA
        // =================================================

        $pd = array_fill(1, 12, 0);

        $kab = array_fill(1, 12, 0);

        $pemda = array_fill(1, 12, 0);

        $kem = array_fill(1, 12, 0);

        foreach ($records as $row) {

            $category = strtolower(
                $row->institutionCategory->name ?? ''
            );

            if (str_contains($category, 'perangkat')) {

                $pd[$row->month] +=
                    $row->total_replications;
            }

            elseif (str_contains($category, 'kabupaten')) {

                $kab[$row->month] +=
                    $row->total_replications;
            }

            elseif (str_contains($category, 'pemda')) {

                $pemda[$row->month] +=
                    $row->total_replications;
            }

            elseif (str_contains($category, 'kementerian')) {

                $kem[$row->month] +=
                    $row->total_replications;
            }
        }

        // =================================================
        // TABLE 3 DATA
        // =================================================

        $jumlahApl = array_fill(1, 12, 0);

        $target = array_fill(1, 12, 0);

        $realisasi = array_fill(1, 12, 0);

        $mentoring = RekayasaMentoringPerformance::where(
            'year',
            $this->year
        )
        ->where(
            'month',
            '<=',
            $this->month
        )
        ->get();

        foreach ($mentoring as $item) {

            $jumlahApl[$item->month] =
                $item->total_apps;

            $target[$item->month] =
                $item->target;

            $realisasi[$item->month] =
                $item->realization;
        }

        // =================================================
        // TABLE 1
        // =================================================

        $sheet->mergeCells('A1:M1');

        $sheet->setCellValue(
            'A1',
            'REKAP REPLIKASI APLIKASI'
        );

        $this->headerStyle($sheet, 'A1');

        $col = 'A';

        foreach ($headers as $header) {

            $sheet->setCellValue(
                $col . '2',
                $header
            );

            $sheet->getStyle($col . '2')->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => 'center'
                ]
            ]);

            $col++;
        }

        $sheet->setCellValue(
            'A3',
            'JUMLAH APLIKASI'
        );

        $this->fillData($sheet, 'B3', $data1);

        // =================================================
        // TABLE 2
        // =================================================

        $sheet->mergeCells('P1:AB1');

        $sheet->setCellValue(
            'P1',
            'REKAP REPLIKASI PER PD/KABKO'
        );

        $this->headerStyle($sheet, 'P1');

        $col = 'Q';

        foreach ($headers as $header) {

            $sheet->setCellValue(
                $col . '2',
                $header
            );

            $sheet->getStyle($col . '2')->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => 'center'
                ]
            ]);

            $col++;
        }

        $sheet->setCellValue('P3', 'PERANGKAT DAERAH');

        $sheet->setCellValue('P4', 'KABUPATEN/KOTA');

        $sheet->setCellValue('P5', 'PEMDA LAINNYA');

        $sheet->setCellValue('P6', 'KEMENTERIAN');

        $this->fillData($sheet, 'R3', $pd);

        $this->fillData($sheet, 'R4', $kab);

        $this->fillData($sheet, 'R5', $pemda);

        $this->fillData($sheet, 'R6', $kem);

        // =================================================
        // TABLE 3
        // =================================================

        $sheet->mergeCells('AF1:AR1');

        $sheet->setCellValue(
            'AF1',
            'PROGRESS PENDAMPINGAN APLIKASI'
        );

        $this->headerStyle($sheet, 'AF1');

        $col = 'AG';

        foreach ($headers as $header) {

            $sheet->setCellValue(
                $col . '2',
                $header
            );

            $sheet->getStyle($col . '2')->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => 'center'
                ]
            ]);

            $col++;
        }

        $sheet->setCellValue('AF3', 'JUMLAH APL');

        $sheet->setCellValue('AF4', 'TARGET');

        $sheet->setCellValue('AF5', 'REALISASI');

        $this->fillData($sheet, 'AG3', $jumlahApl);

        $this->fillData($sheet, 'AG4', $target);

        $this->fillData($sheet, 'AG5', $realisasi);

        // =================================================
        // BORDER
        // =================================================

        $sheet->getStyle('A1:M3')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('P1:AC6')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('AF1:AR5')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =================================================
        // CHART 1
        // =================================================

        $this->createChart(
            $sheet,
            'chart1',
            'REKAP APLIKASI',
            'B2:M2',
            'B3:M3',
            'A8',
            'M20'
        );

        // =================================================
        // CHART 2
        // =================================================

        $this->createChart(
            $sheet,
            'chart2',
            'REKAP PD/KABKO',
            'R2:AC2',
            'R3:AC3',
            'P8',
            'AB20'
        );

        // =================================================
        // CHART 3
        // =================================================

        $this->createChart(
            $sheet,
            'chart3',
            'PROGRESS PENDAMPINGAN',
            'AG2:AR2',
            'AG3:AR3',
            'AF8',
            'AR20'
        );

        // =================================================
        // AUTO SIZE
        // =================================================

        for ($col = 'A'; $col !== 'AS'; $col++) {

            $sheet->getColumnDimension($col)
                ->setAutoSize(true);
        }

        // =================================================
        // SAVE
        // =================================================

        $writer = new Xlsx($spreadsheet);

        $writer->setIncludeCharts(true);

        $path = storage_path(
            'app/public/rekayasa.xlsx'
        );

        $writer->save($path);

        return $path;
    }

    private function headerStyle($sheet, $cell)
    {
        $sheet->getStyle($cell)->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => 'center'
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => [
                    'rgb' => 'FFC000'
                ]
            ]
        ]);
    }

    private function fillData(
        $sheet,
        $startCell,
        $data
    ) {

        preg_match(
            '/([A-Z]+)(\d+)/',
            $startCell,
            $matches
        );

        $col = $matches[1];

        $row = $matches[2];

        foreach ($data as $item) {

            $sheet->setCellValue(
                $col . $row,
                $item
            );

            $col++;
        }
    }

    private function createChart(
        $sheet,
        $name,
        $title,
        $categoryRange,
        $valueRange,
        $topLeft,
        $bottomRight
    ) {

        $categories = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                'Rekap!$' . $categoryRange,
                null,
                12
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'Rekap!$' . $valueRange,
                null,
                12
            )
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values) - 1),
            [],
            $categories,
            $values
        );

        $series->setPlotDirection(
            DataSeries::DIRECTION_COL
        );

        $plotArea = new PlotArea(
            null,
            [$series]
        );

        $legend = new Legend(
            Legend::POSITION_RIGHT,
            null,
            false
        );

        $title = new Title($title);

        $chart = new Chart(
            $name,
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition($topLeft);

        $chart->setBottomRightPosition($bottomRight);

        $sheet->addChart($chart);
    }
}
