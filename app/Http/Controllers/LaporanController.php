<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\RekayasaApplicationExport;
use App\Exports\IntopExport;
use App\Exports\SadajabarExport;
use App\Exports\SmartjabarExport;
use App\Exports\SidebarExport;
use App\Exports\AppmanExport;

class LaporanController extends Controller
{
    public function rekayasaExport(Request $request)
    {
        $year = $request->year ?? now()->year;

        $month = $request->month ?? now()->month;

        $export = new RekayasaApplicationExport(
            $year,
            $month
        );

        $path = $export->export();

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

    public function intopExport(Request $request)
    {
        $year = $request->year ?? now()->year;

        $month = $request->month ?? now()->month;

        $export = new IntopExport(
            $year,
            $month
        );

        $path = $export->export();

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

   public function sadajabarExport(Request $request)
    {
        $year = $request->year ?? now()->year;

        $month = $request->month ?? now()->month;

        $export = new SadajabarExport(
            $year,
            $month
        );

        $path = $export->export();

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

    public function smartjabarExport(Request $request)
    {
        $year = $request->year ?? now()->year;

        $month = $request->month ?? now()->month;

        $export = new SmartjabarExport(
            $year,
            $month
        );

        $path = $export->export();

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

    public function sidebarExport(Request $request)
    {
        $year = $request->year ?? now()->year;

        $month = $request->month ?? now()->month;

        $export = new SidebarExport(
            $year,
            $month
        );

        $path = $export->export();

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

    public function appmanExport(Request $request)
    {
        $year = $request->year ?? now()->year;

        $month = $request->month ?? now()->month;

        $export = new AppmanExport(
            $year,
            $month
        );

        $path = $export->export();

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }
}