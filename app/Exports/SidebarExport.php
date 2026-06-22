<?php

namespace App\Exports;

use App\Models\SidebarMetric;
use App\Models\SidebarDocumentStat;
use App\Models\SidebarOpdUsage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\{
    Border,
    Fill,
    Alignment
};
use PhpOffice\PhpSpreadsheet\Chart\{
    Chart,
    DataSeries,
    DataSeriesValues,
    Legend,
    PlotArea,
    Title
};

class SidebarExport
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

        $sheet->setTitle('SIDEBAR');

        // =====================================================
        // PAGE SETUP
        // =====================================================

        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A3);

        // =====================================================
        // WIDTH
        // =====================================================

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(45);

        foreach (range('C', 'O') as $col) {
            $sheet->getColumnDimension($col)
                ->setWidth(12);
        }

        foreach (range('Q', 'Z') as $col) {
            $sheet->getColumnDimension($col)
                ->setWidth(15);
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

        $sheet->setCellValue(
            'A1',
            'TIM SIDEBAR'
        );

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ]
        ]);

        // =====================================================
        // HEADER TABLE
        // =====================================================

        $sheet->mergeCells('A3:O3');

        $sheet->setCellValue(
            'A3',
            'PRESENTASE PENGGUNA SIDEBAR PADA PD'
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
        // HEADER
        // =====================================================

        $headers = [
            'NO.',
            'OPD',
            'TOTAL USERS',
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

            $sheet->setCellValue(
                $col.'4',
                $header
            );

            $col++;
        }

        $sheet->setCellValue(
            'P4',
            '% AKTIF'
        );

        $sheet->getStyle('A4:P4')->applyFromArray([
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
        // GET DATA OPD
        // =====================================================

        $opdUsage = SidebarOpdUsage::with('opd')
            ->where('year', $this->year)
            ->where('month', '<=', $this->month)
            ->get()
            ->groupBy('opd_id');

        // =====================================================
        // BODY TABLE
        // =====================================================

        $row = 5;
        $no = 1;

        foreach ($opdUsage as $opdId => $items) {

            $opdName = optional(
                $items->first()->opd
            )->name ?? '-';

            $monthlyData = [];

            for ($i = 1; $i <= 12; $i++) {
                $monthlyData[$i] = 0;
            }

            foreach ($items as $item) {

                $monthlyData[$item->month] =
                    $item->active_count;
            }

            // =================================================
            // TOTAL USERS
            // =================================================

            $metric = SidebarMetric::where(
                'year',
                $this->year
            )
            ->where('month', $this->month)
            ->first();

            $totalUsers = $metric
                ? $metric->total_users
                : 0;

            // =================================================
            // TABLE
            // =================================================

            $sheet->setCellValue(
                'A'.$row,
                $no++
            );

            $sheet->setCellValue(
                'B'.$row,
                $opdName
            );

            $sheet->setCellValue(
                'C'.$row,
                $totalUsers
            );

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

            $latestValue =
                $monthlyData[$this->month] ?? 0;

            $percentage = $totalUsers > 0
                ? ($latestValue / $totalUsers) * 100
                : 0;

            $sheet->setCellValue(
                'P'.$row,
                round($percentage, 2)
            );

            // =================================================
            // STYLE
            // =================================================

            $sheet->getStyle(
                "A{$row}:P{$row}"
            )->applyFromArray([
                'font' => [
                    'size' => 9
                ],
                'alignment' => [
                    'vertical' =>
                        Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' =>
                            Border::BORDER_THIN
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
            'TOTAL PENGGUNA SIDEBAR'
        );

        foreach (range('C', 'O') as $col) {

            $sheet->setCellValue(
                $col.$row,
                '=SUM('.$col.'5:'.$col.($row-1).')'
            );
        }

        $sheet->setCellValue(
            'P'.$row,
            '99%'
        );

        $sheet->getStyle(
            "A{$row}:P{$row}"
        )->applyFromArray([
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
                    'borderStyle' =>
                        Border::BORDER_THIN
                ]
            ]
        ]);

        // =====================================================
        // SIDEBAR METRICS
        // =====================================================

        $metrics = SidebarMetric::where(
            'year',
            $this->year
        )
        ->where('month', '<=', $this->month)
        ->get();

        $metricData = [];

        for ($i = 1; $i <= 12; $i++) {

            $metricData[$i] = [
                'total_users' => 0,
                'active_users' => 0,
                'document_created' => 0
            ];
        }

        foreach ($metrics as $metric) {

            $metricData[$metric->month] = [
                'total_users' =>
                    $metric->total_users,
                'active_users' =>
                    $metric->active_users,
                'document_created' =>
                    $metric->document_created
            ];
        }

        // =====================================================
        // RIGHT TABLE 1
        // =====================================================

        $sheet->mergeCells('Q3:R3');

        $sheet->setCellValue(
            'Q3',
            'Total Users Sidebar'
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

        $r = 5;

        for ($i = 1; $i <= $this->month; $i++) {

            $sheet->setCellValue(
                'Q'.$r,
                $months[$i]
            );

            $sheet->setCellValue(
                'R'.$r,
                $metricData[$i]['total_users']
            );

            $r++;
        }

        $sheet->getStyle('Q4:R'.$r)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =====================================================
        // RIGHT TABLE 2
        // =====================================================

        $sheet->mergeCells('Q10:R10');

        $sheet->setCellValue(
            'Q10',
            'Active Users Sidebar'
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

        $rr = 12;

        for ($i = 1; $i <= $this->month; $i++) {

            $sheet->setCellValue(
                'Q'.$rr,
                $months[$i]
            );

            $sheet->setCellValue(
                'R'.$rr,
                $metricData[$i]['active_users']
            );

            $rr++;
        }

        $sheet->getStyle('Q11:R'.$rr)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // =====================================================
        // DOCUMENT STATS
        // =====================================================

        $documentStats = SidebarDocumentStat::with(
            'documentType'
        )
        ->where('year', $this->year)
        ->where('month', $this->month)
        ->get();

        $docStart = 20;

        $sheet->mergeCells(
            "Q{$docStart}:R{$docStart}"
        );

        $sheet->setCellValue(
            "Q{$docStart}",
            'Document Created'
        );

        $sheet->getStyle(
            "Q{$docStart}:R{$docStart}"
        )->applyFromArray([
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

        $docStart++;

        foreach ($documentStats as $doc) {

            $sheet->setCellValue(
                'Q'.$docStart,
                optional(
                    $doc->documentType
                )->name ?? '-'
            );

            $sheet->setCellValue(
                'R'.$docStart,
                $doc->total_count
            );

            $docStart++;
        }

        // =====================================================
        // CHART 1
        // =====================================================

        $this->createChart(
            $sheet,
            'chart1',
            'TOTAL USERS SIDEBAR',
            'Q5:Q'.($r - 1),
            'R5:R'.($r - 1),
            'T3',
            'Z12'
        );

        // =====================================================
        // CHART 2
        // =====================================================

        $this->createChart(
            $sheet,
            'chart2',
            'ACTIVE USERS SIDEBAR',
            'Q12:Q'.($rr - 1),
            'R12:R'.($rr - 1),
            'T13',
            'Z22'
        );

        // =====================================================
        // CHART 3
        // =====================================================

        $this->createChart(
            $sheet,
            'chart3',
            'TOTAL PENGGUNA SIDEBAR',
            'B5:B'.($row - 1),
            'P5:P'.($row - 1),
            'A20',
            'H35'
        );

        // =====================================================
        // CHART 4 HORIZONTAL
        // =====================================================

        $this->createHorizontalChart(
            $sheet,
            'chart4',
            'PENGGUNA SIDEBAR',
            'B5:B'.($row - 1),
            'P5:P'.($row - 1),
            'J20',
            'Z45'
        );
        // =====================================================
        // SAVE
        // =====================================================

        $writer = new Xlsx($spreadsheet);

        $writer->setIncludeCharts(true);

        $path = storage_path(
            'app/public/sidebar.xlsx'
        );

        $writer->save($path);

        return $path;
    }

    // =====================================================
    // CHART VERTICAL
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
                'SIDEBAR!$'.$categoryRange,
                null
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'SIDEBAR!$'.$valueRange,
                null
            )
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values)-1),
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

        $chart->setTopLeftPosition(
            $topLeft
        );

        $chart->setBottomRightPosition(
            $bottomRight
        );

        $sheet->addChart($chart);
    }

    // =====================================================
    // CHART HORIZONTAL
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
                'SIDEBAR!$'.$categoryRange,
                null
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'SIDEBAR!$'.$valueRange,
                null
            )
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values)-1),
            [],
            $categories,
            $values
        );

        $series->setPlotDirection(
            DataSeries::DIRECTION_BAR
        );

        $plotArea = new PlotArea(
            null,
            [$series]
        );

        $title = new Title($title);

        $chart = new Chart(
            $name,
            $title,
            null,
            $plotArea
        );

        $chart->setTopLeftPosition(
            $topLeft
        );

        $chart->setBottomRightPosition(
            $bottomRight
        );

        $sheet->addChart($chart);
    }
}