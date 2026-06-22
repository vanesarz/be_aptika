<?php

namespace App\Exports;

use App\Models\SadajabarAppIntegration;
use App\Models\SadajabarEncryptionStat;
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

class SadajabarExport
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

        $sheet->setTitle('SADAJABAR');

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
        // DATA ENCRYPTION
        // =================================================

        $jumlahData = array_fill(1, 12, 0);

        $encryptions = SadajabarEncryptionStat::where(
            'year',
            $this->year
        )
        ->where(
            'month',
            '<=',
            $this->month
        )
        ->get();

        foreach ($encryptions as $item) {

            $jumlahData[$item->month] =
                $item->app_count;
        }

        // =================================================
        // DATA INTEGRATION
        // =================================================

        $pemprov = array_fill(1, 12, 0);

        $kabko = array_fill(1, 12, 0);

        $lainnya = array_fill(1, 12, 0);

        $integrations =
            SadajabarAppIntegration::with(
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

            $name = strtolower(
                $item->institutionCategory->name ?? ''
            );

            if (
                str_contains($name, 'provinsi')
                ||
                str_contains($name, 'pd')
            ) {

                $pemprov[$month] +=
                    $item->app_count;
            }

            elseif (
                str_contains($name, 'kabupaten')
                ||
                str_contains($name, 'kota')
            ) {

                $kabko[$month] +=
                    $item->app_count;
            }

            else {

                $lainnya[$month] +=
                    $item->app_count;
            }
        }

        // =================================================
        // TITLE
        // =================================================

        $sheet->mergeCells('A1:AC1');

        $sheet->setCellValue(
            'A1',
            'TIM SADAJABAR'
        );

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'horizontal' => 'center'
            ]
        ]);

        // =================================================
        // TABLE 1
        // =================================================

        $sheet->mergeCells('A3:M3');

        $sheet->setCellValue(
            'A3',
            'REKAPITULASI DATA TEREKAM DAN TERENKRIPSI'
        );

        $this->headerStyle($sheet, 'A3:M3');

        $sheet->setCellValue('A4', $this->year);

        $col = 'B';

        foreach ($months as $month) {

            $sheet->setCellValue(
                $col . '4',
                $month
            );

            $col++;
        }

        $sheet->setCellValue(
            'A5',
            'JUMLAH DATA'
        );

        $this->fillHorizontal(
            $sheet,
            'B5',
            $jumlahData
        );

        // BORDER TABLE 1

        $sheet->getStyle('A3:M5')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =================================================
        // TABLE 2
        // =================================================

        $sheet->mergeCells('P3:AC3');

        $sheet->setCellValue(
            'P3',
            'REKAPITULASI APLIKASI TERINTEGRASI SADAJABAR'
        );

        $this->headerStyle($sheet, 'P3:AC3');

        $sheet->setCellValue('P4', $this->year);

        $col = 'Q';

        foreach ($months as $month) {

            $sheet->setCellValue(
                $col . '4',
                $month
            );

            $col++;
        }

        $sheet->setCellValue(
            'P5',
            'PEMPROV (PD)'
        );

        $sheet->setCellValue(
            'P6',
            'KABKO'
        );

        $sheet->setCellValue(
            'P7',
            'K/L/LAINNYA'
        );

        $this->fillHorizontal(
            $sheet,
            'Q5',
            $pemprov
        );

        $this->fillHorizontal(
            $sheet,
            'Q6',
            $kabko
        );

        $this->fillHorizontal(
            $sheet,
            'Q7',
            $lainnya
        );

        // BORDER TABLE 2

        $sheet->getStyle('P3:AC7')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =================================================
        // CHART 1
        // =================================================

        $this->createChart(
            $sheet,
            'chart1',
            'DATA TEREKAM',
            'B4:M4',
            'B5:M5',
            'A10',
            'M25'
        );

        // =================================================
        // CHART 2
        // =================================================

        $this->createChart(
            $sheet,
            'chart2',
            'APLIKASI TERINTEGRASI',
            'Q4:AC4',
            'Q5:AC5',
            'P10',
            'AC25'
        );

        // =================================================
        // AUTO SIZE
        // =================================================

        for ($col = 'A'; $col !== 'AD'; $col++) {

            $sheet->getColumnDimension($col)
                ->setAutoSize(true);
        }

        // =================================================
        // SAVE
        // =================================================

        $writer = new Xlsx($spreadsheet);

        $writer->setIncludeCharts(true);

        $path = storage_path(
            'app/public/sadajabar.xlsx'
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
                'SADAJABAR!$' . $categoryRange,
                null,
                12
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'SADAJABAR!$' . $valueRange,
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