<?php

namespace App\Exports;

use App\Models\IntopIntegrationSummary;
use App\Models\IntopMandateServiceSummary;
use App\Models\IntopServiceCatalog;
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

class IntopExport
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

        $sheet->setTitle('INTOP');

        // =================================================
        // LANDSCAPE
        // =================================================

        $sheet->getPageSetup()->setOrientation(
            PageSetup::ORIENTATION_LANDSCAPE
        );

        // =================================================
        // MONTH HEADER
        // =================================================

        $months = [
            1 => 'JAN',
            2 => 'FEB',
            3 => 'MAR',
            4 => 'APR',
            5 => 'MEI',
            6 => 'JUN',
            7 => 'JUL',
            8 => 'AGS',
            9 => 'SEP',
            10 => 'OKT',
            11 => 'NOV',
            12 => 'DES'
        ];

        // =================================================
        // DATA EKOSISTEM
        // =================================================

        $services =
            IntopMandateServiceSummary::where(
                'year',
                $this->year
            )
            ->get();

        $administrasi = $services
            ->where('category', 'administrasi');

        $publik = $services
            ->where('category', 'publik');

        // =================================================
        // DATA KATALOG
        // =================================================

        $admService = array_fill(1, 12, 0);

        $publicService = array_fill(1, 12, 0);

        $target = array_fill(1, 12, 0);

        $achievement = array_fill(1, 12, 0);

        $percent = array_fill(1, 12, 0);

        $catalogs = IntopServiceCatalog::where(
            'year',
            $this->year
        )
        ->where(
            'month',
            '<=',
            $this->month
        )
        ->get();

        foreach ($catalogs as $item) {

            $admService[$item->month] =
                $item->adm_service_count;

            $publicService[$item->month] =
                $item->public_service_count;

            $target[$item->month] =
                $item->target_abs;

            $achievement[$item->month] =
                $item->achievement_abs;

            if ($item->target_abs > 0) {

                $percent[$item->month] =
                    round(
                        ($item->achievement_abs /
                        $item->target_abs) * 100
                    );
            }
        }

        // =================================================
        // DATA INTEGRASI
        // =================================================

        $total = array_fill(1, 12, 0);

        $kabupaten = array_fill(1, 12, 0);

        $kementerian = array_fill(1, 12, 0);

        $pemprov = array_fill(1, 12, 0);

        $integrations =
            IntopIntegrationSummary::with(
                'institutionCategory'
            )
            ->where(
                'year',
                $this->year
            )
            ->where(
                'month',
                '<=',
                $this->month
            )
            ->get();

        foreach ($integrations as $item) {

            $month = $item->month;

            $total[$month] +=
                $item->app_count;

            $name = strtolower(
                $item->institutionCategory->name ?? ''
            );

            if (str_contains($name, 'kabupaten')) {

                $kabupaten[$month] +=
                    $item->app_count;
            }

            elseif (str_contains($name, 'kementerian')) {

                $kementerian[$month] +=
                    $item->app_count;
            }

            elseif (str_contains($name, 'provinsi')) {

                $pemprov[$month] +=
                    $item->app_count;
            }
        }

        // =================================================
        // TITLE LEFT
        // =================================================

        $sheet->mergeCells('A1:H1');

        $sheet->setCellValue(
            'A1',
            'EKOSISTEM LAYANAN TERINTEGRASI DAN INTEROPERABILITAS DATA MELALUI SPLP (' . $this->year . ')'
        );

        $this->headerStyle($sheet, 'A1:H1');

        // =================================================
        // TABLE ADMINISTRASI
        // =================================================

        $sheet->setCellValue('A2', 'NO');

        $sheet->setCellValue(
            'B2',
            'LAYANAN ADMINISTRASI'
        );

        $sheet->setCellValue('E2', 'NO');

        $sheet->setCellValue(
            'F2',
            'LAYANAN PUBLIK'
        );

        $this->tableHeader($sheet, 'A2:B2');

        $this->tableHeader($sheet, 'E2:F2');

        $rowAdm = 3;

        $no = 1;

        foreach ($administrasi as $item) {

            $sheet->setCellValue(
                'A' . $rowAdm,
                $no++
            );

            $sheet->setCellValue(
                'B' . $rowAdm,
                $item->service_name
            );

            $rowAdm++;
        }

        $rowPub = 3;

        $no = 1;

        foreach ($publik as $item) {

            $sheet->setCellValue(
                'E' . $rowPub,
                $no++
            );

            $sheet->setCellValue(
                'F' . $rowPub,
                $item->service_name
            );

            $rowPub++;
        }

        // =================================================
        // BORDER LEFT
        // =================================================

        $maxLeft = max($rowAdm, $rowPub);

        $sheet->getStyle(
            'A2:F' . ($maxLeft - 1)
        )
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

        // =================================================
        // TITLE CENTER
        // =================================================

        $sheet->mergeCells('J1:V1');

        $sheet->setCellValue(
            'J1',
            'JUMLAH EKOSISTEM LAYANAN'
        );

        $this->headerStyle($sheet, 'J1:V1');

        $sheet->setCellValue('J2', '');

        $col = 'K';

        foreach ($months as $month) {

            $sheet->setCellValue(
                $col . '2',
                $month
            );

            $col++;
        }

        $sheet->setCellValue(
            'J3',
            'LAY. ADM PEM'
        );

        $sheet->setCellValue(
            'J4',
            'LAY PUBLIK'
        );

        $sheet->setCellValue(
            'J6',
            'TARGET'
        );

        $sheet->setCellValue(
            'J7',
            'CAPAIAN'
        );

        $sheet->setCellValue(
            'J9',
            'PERSENTASE'
        );

        $this->fillHorizontal(
            $sheet,
            'K3',
            $admService
        );

        $this->fillHorizontal(
            $sheet,
            'K4',
            $publicService
        );

        $this->fillHorizontal(
            $sheet,
            'K6',
            $target
        );

        $this->fillHorizontal(
            $sheet,
            'K7',
            $achievement
        );

        $this->fillHorizontal(
            $sheet,
            'K9',
            $percent
        );

        $sheet->getStyle('J1:V9')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =================================================
        // TITLE RIGHT
        // =================================================

        $sheet->mergeCells('X1:AK1');

        $sheet->setCellValue(
            'X1',
            'REKAP JUMLAH APLIKASI TERINTEGRASI'
        );

        $this->headerStyle($sheet, 'X1:AK1');

        $sheet->setCellValue('X2', '');

        $col = 'Y';

        foreach ($months as $month) {

            $sheet->setCellValue(
                $col . '2',
                $month
            );

            $col++;
        }

        $sheet->setCellValue('X3', 'TOTAL');

        $sheet->setCellValue(
            'X4',
            'KABUPATEN/KOTA'
        );

        $sheet->setCellValue(
            'X5',
            'KEMENTERIAN'
        );

        $sheet->setCellValue(
            'X6',
            'PEMPROV'
        );

        $this->fillHorizontal(
            $sheet,
            'Y3',
            $total
        );

        $this->fillHorizontal(
            $sheet,
            'Y4',
            $kabupaten
        );

        $this->fillHorizontal(
            $sheet,
            'Y5',
            $kementerian
        );

        $this->fillHorizontal(
            $sheet,
            'Y6',
            $pemprov
        );

        $sheet->getStyle('X1:AK6')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =================================================
        // CHART CENTER
        // =================================================

        $this->createChart(
            $sheet,
            'chart1',
            'REKAP EKOSISTEM LAYANAN',
            'K2:V2',
            'K3:V3',
            'J12',
            'V28'
        );

        // =================================================
        // CHART RIGHT
        // =================================================

        $this->createChart(
            $sheet,
            'chart2',
            'REKAP APLIKASI TERINTEGRASI',
            'Y2:AK2',
            'Y3:AK3',
            'X12',
            'AK28'
        );

        // =================================================
        // AUTO SIZE
        // =================================================

        for ($col = 'A'; $col !== 'AL'; $col++) {

            $sheet->getColumnDimension($col)
                ->setAutoSize(true);
        }

        // =================================================
        // SAVE
        // =================================================

        $writer = new Xlsx($spreadsheet);

        $writer->setIncludeCharts(true);

        $path = storage_path(
            'app/public/intop.xlsx'
        );

        $writer->save($path);

        return $path;
    }

    private function fillHorizontal(
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

    private function tableHeader(
        $sheet,
        $range
    ) {

        $sheet->getStyle($range)
            ->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => 'center'
                ]
            ]);
    }

    private function headerStyle(
        $sheet,
        $range
    ) {

        $sheet->getStyle($range)
            ->applyFromArray([
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
                'INTOP!$' . $categoryRange,
                null,
                12
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'INTOP!$' . $valueRange,
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
