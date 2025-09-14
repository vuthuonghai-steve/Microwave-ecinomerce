<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $service)
    {
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

        if ($format !== 'csv') {
            return response()->json(['message' => 'Only CSV supported'], 400);
        }

        $csv = $this->service->exportCsv($type, $data);
        $filename = sprintf('%s_%s.csv', $type, now()->format('Ymd_His'));
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
