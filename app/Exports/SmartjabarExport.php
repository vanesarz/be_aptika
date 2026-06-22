<?php

namespace App\Exports;

use App\Models\SmartjabarJoinedApp;
use App\Models\SmartjabarUsageStat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\{
    Chart,
    DataSeries,
    DataSeriesValues,
    Legend,
    PlotArea,
    Title
};

class SmartjabarExport
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

        $sheet->setTitle('SMARTJABAR');

        // =====================================================
        // PAGE SETUP
        // =====================================================

        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A3);

        // =====================================================
        // COLUMN WIDTH
        // =====================================================

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(50);

        foreach (range('C', 'O') as $col) {
            $sheet->getColumnDimension($col)->setWidth(10);
        }

        foreach (range('Q', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setWidth(14);
        }

        // =====================================================
        // MONTHS
        // =====================================================

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

        // =====================================================
        // TITLE
        // =====================================================

        $sheet->mergeCells('A1:Z1');

        $sheet->setCellValue('A1', 'TIM SMART JABAR');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ]
        ]);

        // =====================================================
        // HEADER
        // =====================================================

        $sheet->mergeCells('A3:O3');

        $sheet->setCellValue(
            'A3',
            'PRESENTASE PENGGUNA SMART JABAR PADA PD'
        );

        $sheet->getStyle('A3:O3')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFC000'
                ]
            ]
        ]);

        // =====================================================
        // TABLE HEADER
        // =====================================================

        $headers = [
            'NO.',
            'OPD',
            'JUMLAH ASN',
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

        $col = 'A';

        foreach ($headers as $header) {

            $sheet->setCellValue($col . '4', $header);

            $col++;
        }

        $sheet->getStyle('A4:O4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFD966'
                ]
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // =====================================================
        // GET DATA FROM DATABASE
        // =====================================================

        $usageStats = SmartjabarUsageStat::with('opd')
            ->where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->groupBy('opd_id');

        // =====================================================
        // BODY TABLE
        // =====================================================

        $row = 5;
        $no = 1;

        foreach ($usageStats as $opdId => $items) {

            $opdName = optional($items->first()->opd)->name ?? '-';

            $totalAsn = $items->sum('total_asn');

            $monthlyData = [];

            for ($i = 1; $i <= 12; $i++) {

                $monthlyData[$i] = 0;
            }

            foreach ($items as $item) {

                $monthlyData[$item->month] =
                    $item->active_users;
            }

            // =================================================
            // TABLE DATA
            // =================================================

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $opdName);
            $sheet->setCellValue('C'.$row, $totalAsn);

            $colMonth = 'D';

            for ($m = 1; $m <= 12; $m++) {

                $sheet->setCellValue(
                    $colMonth.$row,
                    $monthlyData[$m]
                );

                $colMonth++;
            }

            // =================================================
            // PERCENTAGE
            // =================================================

            $latestValue = $monthlyData[$this->month] ?? 0;

            $percentage = $totalAsn > 0
                ? ($latestValue / $totalAsn) * 100
                : 0;

            $sheet->setCellValue(
                'O'.$row,
                round($percentage, 2)
            );

            // =================================================
            // STYLE
            // =================================================

            $sheet->getStyle("A{$row}:O{$row}")
                ->applyFromArray([
                    'font' => [
                        'size' => 9
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ]
                ]);

            $row++;
        }

        // =====================================================
        // TOTAL ROW
        // =====================================================

        $sheet->mergeCells("A{$row}:B{$row}");

        $sheet->setCellValue(
            "A{$row}",
            'TOTAL PENGGUNA SMARTJABAR'
        );

        $sheet->setCellValue(
            "C{$row}",
            '=SUM(C5:C'.($row-1).')'
        );

        foreach (range('D', 'N') as $col) {

            $sheet->setCellValue(
                $col.$row,
                '=SUM('.$col.'5:'.$col.($row-1).')'
            );
        }

        $sheet->setCellValue(
            "O{$row}",
            '99%'
        );

        $sheet->getStyle("A{$row}:O{$row}")
            ->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'FFC000'
                    ]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);

        // =====================================================
        // JOINED APPS DATA
        // =====================================================

        $joinedApps = SmartjabarJoinedApp::where(
            'year',
            $this->year
        )->get();

        $joinedData = [];

        for ($i = 1; $i <= 12; $i++) {

            $joinedData[$i] = 0;
        }

        foreach ($joinedApps as $item) {

            $joinedData[$item->month] =
                $item->total_apps;
        }

        // =====================================================
        // RIGHT TABLE 1
        // =====================================================

        $sheet->mergeCells('Q3:R3');

        $sheet->setCellValue(
            'Q3',
            'Aplikasi Tergabung SmartJabar'
        );

        $sheet->getStyle('Q3:R3')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFC000'
                ]
            ]
        ]);

        $sheet->setCellValue('Q4', '2025');
        $sheet->setCellValue('R4', 'Jumlah');

        $rightRow = 5;

        for ($i = 1; $i <= $this->month; $i++) {

            $sheet->setCellValue(
                'Q'.$rightRow,
                $months[$i]
            );

            $sheet->setCellValue(
                'R'.$rightRow,
                $joinedData[$i]
            );

            $rightRow++;
        }

        $sheet->getStyle('Q4:R'.$rightRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =====================================================
        // TOTAL ACTIVE USERS
        // =====================================================

        $sheet->mergeCells('Q10:R10');

        $sheet->setCellValue(
            'Q10',
            'Jumlah Pengguna SmartJabar'
        );

        $sheet->getStyle('Q10:R10')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFC000'
                ]
            ]
        ]);

        $sheet->setCellValue('Q11', '2025');
        $sheet->setCellValue('R11', 'Jumlah');

        $usageMonthly = [];

        for ($i = 1; $i <= 12; $i++) {

            $usageMonthly[$i] = SmartjabarUsageStat::where(
                'year',
                $this->year
            )
            ->where('month', $i)
            ->sum('active_users');
        }

        $usageRow = 12;

        for ($i = 1; $i <= $this->month; $i++) {

            $sheet->setCellValue(
                'Q'.$usageRow,
                $months[$i]
            );

            $sheet->setCellValue(
                'R'.$usageRow,
                $usageMonthly[$i]
            );

            $usageRow++;
        }

        $sheet->getStyle('Q11:R'.$usageRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =====================================================
        // CHART 1
        // =====================================================

        $this->createChart(
            $sheet,
            'chart1',
            'APLIKASI TERGABUNG SMARTJABAR',
            'Q5:Q'.($rightRow-1),
            'R5:R'.($rightRow-1),
            'T3',
            'Z12'
        );

        // =====================================================
        // CHART 2
        // =====================================================

        $this->createChart(
            $sheet,
            'chart2',
            'PENGGUNA SMARTJABAR',
            'Q12:Q'.($usageRow-1),
            'R12:R'.($usageRow-1),
            'T13',
            'Z22'
        );

        // =====================================================
        // CHART 3
        // =====================================================

        $this->createChart(
            $sheet,
            'chart3',
            'TOTAL PENGGUNA SMARTJABAR',
            'D4:F4',
            'D'.$row.':F'.$row,
            'A20',
            'H35'
        );

        // =====================================================
        // CHART 4 HORIZONTAL
        // =====================================================

        $this->createHorizontalChart(
            $sheet,
            'chart4',
            'PENGGUNA SMARTJABAR',
            'B5:B'.($row-1),
            'O5:O'.($row-1),
            'J20',
            'Z45'
        );

        // =====================================================
        // SAVE FILE
        // =====================================================

        $writer = new Xlsx($spreadsheet);

        $writer->setIncludeCharts(true);

        $path = storage_path(
            'app/public/smartjabar.xlsx'
        );

        $writer->save($path);

        return $path;
    }

    // =====================================================
    // VERTICAL CHART
    // =====================================================

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
                'SMARTJABAR!$'.$categoryRange,
                null
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'SMARTJABAR!$'.$valueRange,
                null
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

        $plotArea = new PlotArea(null, [$series]);

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

    // =====================================================
    // HORIZONTAL CHART
    // =====================================================

    private function createHorizontalChart(
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
                'SMARTJABAR!$'.$categoryRange,
                null
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'SMARTJABAR!$'.$valueRange,
                null
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
            DataSeries::DIRECTION_BAR
        );

        $plotArea = new PlotArea(null, [$series]);

        // HAPUS LEGEND
        $legend = null;

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