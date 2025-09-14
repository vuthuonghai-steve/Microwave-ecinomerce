<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $service)
    {
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function revenue(Request $request)
    {
        $range = $request->query('range', 'daily');
        $from = $request->query('from');
        $to = $request->query('to');
        return response()->json($this->service->revenue($range, $from, $to));
    }

    public function bestSelling(Request $request)
    {
        $range = $request->query('range', 'daily');
        $from = $request->query('from');
        $to = $request->query('to');
        $limit = (int) $request->query('limit', 10);
        return response()->json($this->service->bestSelling($range, $from, $to, $limit));
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'revenue');
        $format = $request->query('format', 'csv');
        $range = $request->query('range', 'daily');
        $from = $request->query('from');
        $to = $request->query('to');
        $limit = (int) $request->query('limit', 10);

        if ($type === 'best-selling') {
            $data = $this->service->bestSelling($range, $from, $to, $limit);
        } else {
            $data = $this->service->revenue($range, $from, $to);
            $type = 'revenue';
        }

        if ($format === 'xlsx' && class_exists('Maatwebsite\\Excel\\Facades\\Excel')) {
            // Build 2D array for Excel
            if ($type === 'revenue') {
                $rows = [['Label','Revenue','Orders']];
                foreach ($data['labels'] as $i => $label) {
                    $rows[] = [$label, $data['revenue'][$i] ?? 0, $data['orders'][$i] ?? 0];
                }
            } else {
                $rows = [['Product','Sold']];
                foreach ($data['top_products'] as $p) { $rows[] = [$p['name'], $p['sold']]; }
            }
            $export = new \App\Exports\ArrayExport($rows, ucfirst(str_replace('-', ' ', $type)));
            return \Maatwebsite\Excel\Facades\Excel::download($export, sprintf('%s_%s.xlsx', $type, now()->format('Ymd_His')));
        }

        // Default CSV
        $csv = $this->service->exportCsv($type, $data);
        $ext = $format === 'xlsx' ? 'xlsx' : 'csv';
        $contentType = $format === 'xlsx' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'text/csv; charset=UTF-8';
        $filename = sprintf('%s_%s.%s', $type, now()->format('Ymd_His'), $ext);
        return response($csv, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
